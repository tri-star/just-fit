<?php

namespace UrbanTheory\JustFit\Engine;

use Symfony\Component\BrowserKit\Client;

/**
 * ZOZOTOWNの検索結果ページをスクレイピングして
 * 検索結果件数や商品のURL一覧を抽出するクラス
 */
class ResultPageParser {
    
    public function __construct() {
    }
    
    /**
     * 検索結果HTMLに含まれる、ヒット件数を抽出して返す。
     * @param string $html
     * @return int ヒットした商品数
     */
    public function extractProductCount($html) {
        
    }
    
    
    /**
     * 検索結果HTMLに含まれる商品詳細ページへのURLを配列で返す。
     * @param string $html
     * @return array 検索結果のURLの配列
     */
    public function extractProductUrls($html) {
    }
}

