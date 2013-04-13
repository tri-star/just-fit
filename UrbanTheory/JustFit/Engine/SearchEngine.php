<?php

namespace UrbanTheory\JustFit\Engine;


use Goutte\Client;
use UrbanTheory\JustFit\Model\Condition;
use UrbanTheory\JustFit\Model\Product;
use Guzzle\Http\Message\Response;


/**
 * ZOZOTOWNのTシャツ専用の検索エンジン。
 * 指定された検索条件でZOZOTOWNへ検索を行い、検索結果画面をスクレイピングして
 * Productオブジェクトを生成する。
 */
class SearchEngine {
    
    const OPTION_WEIGHT_PER_PRODUCT = 'weight_per_product';
    
    const OPTION_MAX_PAGES = 0;
    
    /**
     * 検索中に発生するイベントを監視するオブジェクトの配列
     * @param array(SearchEventHandler)
     */
    private $eventHandlers;
    
    /**
     * 
     * @var ResultPageParser
     */
    private $resultPageParser;
    
    /**
     * 
     * @var ProductPageParser
     */
    private $productPageParser;
    
    /**
     * 商品が検索条件として表示するべきかを判定するオブジェクト
     * @var FilterChain
     */
    private $filterChain;
    
    /**
     * HTTPクライアント。ZOZOTOWNを解析する場合、このクライアントは
     * レスポンスに含まれるCookieを適切に扱える必要がある。
     * @var Client
     */
    private $client;
    
    /**
     * 設定情報
     * @var array
     */
    private $options;
    
    
    public function __construct(ResultPageParser $resultPageParser=null, ProductPageParser $productPageParser=null, Client $client=null) {
        $this->resultPageParser = $resultPageParser;
        if(is_null($this->resultPageParser)) {
            $this->resultPageParser = new ResultPageParser();
        }
        
        $this->productPageParser = $productPageParser;
        if(is_null($this->productPageParser)) {
            $this->productPageParser = new ProductPageParser();
        }
        
        $this->client = $client;
        if(is_null($this->client)) {
            $this->client = new Client();
        }
        
        $this->filterChain = new FilterChain();
        
        $this->init();
    } 
    
    public function init($options = array()) {
        $this->eventHandlers = array();
        
        $this->options = array(
            self::OPTION_WEIGHT_PER_PRODUCT => 5 * 1000 * 1000,
            self::OPTION_MAX_PAGES => 0,
        );
        $this->setOptions($options);
    }
    
    public function search(Condition $condition) {
        
        $this->client->restart();
        
        //件数を取得
        $resultPageResponse = $this->getResponseWithCondition($condition);
        $numProducts = $this->resultPageParser->extractProductCount($resultPageResponse->getContent());
        
        //ページ数の特定(今は30件だと仮定している)
        $numPages = (int)($numProducts / 30) + (($numProducts % 30) ? 1 : 0);
        if(isset($this->options[self::OPTION_MAX_PAGES]) && $this->options[self::OPTION_MAX_PAGES] > 0) {
            $numPages = min($numPages, $this->options[self::OPTION_MAX_PAGES]);
        }
        
        $this->onSearchStart($numProducts, $numPages);
        
        //ページ単位でループ
        $parseCount = 1;
        for($i=1; $i<=$numPages; $i++) {
            $this->onParseResultPageStart($i);
            
            //検索結果ページのHTMLを取得
            //商品URLの一覧を取得
            $condition->set('page', $i);
            $resultPageResponse = $this->getResponseWithCondition($condition);
            
            $productUrls = $this->resultPageParser->extractProductUrls($resultPageResponse->getContent());
            //商品URL単位でループ
            foreach($productUrls as $productUrl) {
                //商品情報を取得
                $this->onParseProductStart($parseCount);
                try {
                    $response = $this->getResponseFromUrl($this->client, $productUrl);
                    $product = $this->productPageParser->extract($response->getContent());
                    $matched = $this->filterChain->isMatch($product);
                    $this->onParseProduct($product, $matched);
                } catch(\Exception $e) {
                    $this->onParseProductFail($productUrl, $e);
                }
                $parseCount++;
                
                if(isset($this->options[self::OPTION_WEIGHT_PER_PRODUCT]) && $this->options[self::OPTION_WEIGHT_PER_PRODUCT] > 0) {
                    usleep($this->options[self::OPTION_WEIGHT_PER_PRODUCT]);
                }
            }
        }
        $this->onSearchDone();
    }
    
    public function addEventHandler($handler) {
        $this->eventHandlers[] = $handler;
    }
    
    
    public function addFilter(FilterInterface $filter) {
        $this->filterChain->addFilter($filter);
    }
    
    
    public function setOptions($newOptions) {
        foreach($newOptions as $key=>$value) {
            $this->options[$key] = $value;
        }
    }
    
    /**
     * 指定した検索条件で検索を実行した結果のHTTPレスポンスオブジェクトを返す。
     * @param Condition $condition 検索条件
     * @return Response
     */
    protected function getResponseWithCondition(Condition $condition) {
        $urlBuilder = new UrlBuilder();
        $hostSearchUrl = $urlBuilder->buildHostSearchUrl($condition);
        
        $response = $this->getResponseFromUrl($hostSearchUrl);
        
        return $response;
    }
    
    
    /**
     * 指定されたURLにアクセスした結果のレスポンスオブジェクトを返す。
     * @param Client $client
     * @param string $url
     * @throws NetworkException
     * @return \Symfony\Component\BrowserKit\Response
     */
    protected function getResponseFromUrl($url) {
        $this->client->request('GET', $url);
        $response = $this->client->getResponse();
        if($response->getStatus() != 200) {
            throw new NetworkException('Staus code error: ' . $response->getStatus());
        }
        return $response;
    }
    
    
    protected function onSearchStart($numProducts, $numPages) {
        foreach($this->eventHandlers as $handler) {
            $handler->onSearchStart($numProducts, $numPages);
        }
    }
    
    
    protected function onParseResultPageStart($pageNo) {
        foreach($this->eventHandlers as $handler) {
            $handler->onParseResultPageStart($pageNo);
        }
    }
    
    protected function onParseProductStart($productCount) {
        foreach($this->eventHandlers as $handler) {
            $handler->onParseProductStart($productCount);
        }
    }
    
    protected function onParseProduct(Product $product, $matched) {
        foreach($this->eventHandlers as $handler) {
            $handler->onParseProduct($product, $matched);
        }
    }
    
    protected function onParseProductFail($url, \Exception $e) {
        foreach($this->eventHandlers as $handler) {
            $handler->onParseProductFail($url, $e);
        }
    }
    
    protected function onSearchDone() {
        foreach($this->eventHandlers as $handler) {
            $handler->onSearchDone();
        }
    }
}