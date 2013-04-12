<?php

namespace UrbanTheory\JustFit\Engine;


use Goutte\Client;
use UrbanTheory\JustFit\Model\Condition;
use UrbanTheory\JustFit\Model\Product;


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
        $numProducts = $this->getNumPages($condition);
        
        //ページ数の特定(今は30件だと仮定している)
        $numPages = (int)($numProducts / 30);
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
            
            $urlBuilder = new UrlBuilder();
            $pagedUrl = $urlBuilder->buildHostSearchUrl($condition);
            $html = $this->getResponseFromUrl($pagedUrl);
            
            $productUrls = $this->resultPageParser->extractProductUrls($html);
            //商品URL単位でループ
            foreach($productUrls as $productUrl) {
                //商品情報を取得
                $this->onParseProductStart($parseCount);
                
                $response = $this->getResponseFromUrl($this->client, $productUrl);
                if($response->getStatus() != 200) {
                    throw new NetworkException('Staus code error: ' . $response->getStatus());
                }
                $product = $this->productPageParser->extract($response->getContent());
                
                $this->onParseProduct($product);
                //or
                //on商品解析失敗
                $parseCount++;
                
                if(isset($this->options[self::OPTION_WEIGHT_PER_PRODUCT]) && $this->options[self::OPTION_WEIGHT_PER_PRODUCT] > 0) {
                    usleep($this->options[self::OPTION_WEIGHT_PER_PRODUCT]);
                }
            }
        }
        //onSearchDone
    }
    
    public function addEventHandler($handler) {
        $this->eventHandlers[] = $handler;
    }
    
    
    public function setOptions($newOptions) {
        foreach($newOptions as $key=>$value) {
            $this->options[$key] = $value;
        }
    }
    
    /**
     * 検索条件に合致する商品が何件存在するかを返す。
     * @param Condition $condition 検索条件
     */
    protected function getNumPages(Condition $condition) {
        $urlBuilder = new UrlBuilder();
        $hostSearchUrl = $urlBuilder->buildHostSearchUrl($condition);
        
        $response = $this->getResponseFromUrl($hostSearchUrl);
        
        return $this->resultPageParser->extractProductCount($response->getContent());
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
    
    protected function onParseProduct(Product $product) {
        foreach($this->eventHandlers as $handler) {
            $handler->onParseProduct($product);
        }
    }
    
}