<?php

namespace UrbanTheory\JustFit\Engine;


use Symfony\Component\BrowserKit\Client;
use UrbanTheory\JustFit\Model\Condition;


/**
 * ZOZOTOWNのTシャツ専用の検索エンジン。
 * 指定された検索条件でZOZOTOWNへ検索を行い、検索結果画面をスクレイピングして
 * Productオブジェクトを生成する。
 */
class SearchEngine {
    
    /**
     * 検索中に発生するイベントを監視するオブジェクトの配列
     * @param array(SearchEventHandler)
     */
    private $eventHandlers;
    
    
    public function __construct() {
        $this->init();
    } 
    
    public function init() {
        $this->eventHandlers = array();
    }
    
    
    public function search(Condition $condition) {
        
        $client = new Client();
        $resultPageParser = new ResultPageParser($client);
        
        //件数を取得
        $numProducts = $this->getNumPages($resultPageParser, $condition);
        
        //ページ数の特定(今は30件だと仮定している)
        $numPages = $numProducts / 30;
        
        $this->onSearchStart($numProducts, $numPages);
        
        //ページ単位でループ
        $parseCount = 1;
        for($i=1; $i<=$numPages; $i++) {
            $this->onParseResultPageStart($i);
            
            //検索結果ページのHTMLを取得
            //商品URLの一覧を取得
            $html = '';
            $productUrls = $resultPageParser->extractProductUrls($html);
            //商品URL単位でループ
            foreach($productUrls as $productUrl) {
                //商品情報を取得
                $this->onParseProductStart($productCount);
                //on商品解析完了
                //or
                //on商品解析失敗
                $parseCount++;
            }
        }
        //onSearchDone
    }
    
    public function addEventHandler(SearchEventHandler $handler) {
        $this->eventHandlers[] = $handler;
    }
    
    
    /**
     * 検索条件に合致する商品が何件存在するかを返す。
     * @param ResultPageParser $resultPageParser 検索結果ページの解析エンジン
     * @param Condition $condition 検索条件
     */
    protected function getNumPages(ResultPageParser $resultPageParser, Condition $condition) {
        $urlBuilder = new UrlBuilder();
        $hostSearchUrl = $urlBuilder->buildHostSearchUrl($condition);
        $html = '';
        return $resultPageParser->extractProductCount($html);
    }
    
    
    protected function onSearchStart($numProducts, $numPages) {
        foreach($this->eventHandlers as $handler) {
            $handler->onSearchStart($numProducts, $numPages);
        }
    }
    
    
    protected function onParseResultPageStart($pageNo) {
        foreach($this->eventHandlers as $handler) {
            $handler->onSearchStart($pageNo);
        }
    }
    
    protected function onParseProductStart($productCount) {
        foreach($this->eventHandlers as $handler) {
            $handler->onParseProductStart($productCount);
        }
    }
    
}