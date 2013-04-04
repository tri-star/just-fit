<?php

namespace UrbanTheory\JustFit\Engine;

use Symfony\Component\BrowserKit\Client;
use Symfony\Component\DomCrawler\Crawler;

/**
 * ZOZOTOWNの検索結果ページをスクレイピングして
 * 検索結果件数や商品のURL一覧を抽出するクラス
 */
class ResultPageParser {
    
    const PRODUCT_URL_PREFIX = 'http://zozo.jp';
    
    public function __construct() {
    }
    
    /**
     * 検索結果HTMLに含まれる、ヒット件数を抽出して返す。
     * @param string $html
     * @return int ヒットした商品数
     */
    public function extractProductCount($html) {
        $crawler = new Crawler($html);
        $crawler = $crawler->filterXPath('//div[@class="sectionHeader clearfix"]/h2');
        
        $nodeText = $crawler->text();
        
        //件数の情報を正規表現で抜き出す
        $matches = array();
        if(!preg_match('/検索結果：([0-9]+)件/', $nodeText, $matches)) {
            throw new SearchResultException('件数情報を取得出来ませんでした。');
        }
        
        return $matches[1];
    }
    
    
    /**
     * 検索結果HTMLに含まれる商品詳細ページへのURLを配列で返す。
     * @param string $html
     * @return array 検索結果のURLの配列
     */
    public function extractProductUrls($html) {
        $crawler = new Crawler($html);
        
        //検索結果の総ページ数を取得する
        $crawler = $crawler->filterXPath('//ul[@id="searchResultList"]/li/div[@class="listInner"]/p[@class="thumb"]/a/@href');
        
        //個々の商品のURLを取得する。
        $productUrls = array();
        
        foreach($crawler as $idx=>$node) {
            $productUrls[] = self::PRODUCT_URL_PREFIX . $node->nodeValue;
        }
        
        return $productUrls;
    }
}

