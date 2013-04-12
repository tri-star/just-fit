<?php


namespace UrbanTheory\JustFit\Engine;


use UrbanTheory\JustFit\Model\Product;

/**
 * 検索エンジンの検索処理過程で呼び出されるオブジェクト
 */
class SearchEventHandler {
    
    
    /**
     * 検索を開始し、ヒット数が分かった段階で呼び出される
     * @param int $productCount 検索結果件数
     * @param int $pageCount    ページ数
     */
    public function onSearchStart($productCount, $pageCount) {
    }
    
    
    /**
     * 検索結果ページの解析を開始した時に呼び出される処理
     * @param int $pageNo 解析を開始するページ番号
     */
    public function onParseResultPageStart($pageNo) {
    }
    
    
    /**
     * 商品の詳細情報の解析を開始した時に呼び出される処理
     * @param int $productCount 解析を開始する商品の番号(1からの連番)
     */
    public function onParseProductStart($productCount) {
    }
    
    
    /**
     * 商品の解析が完了した時に呼び出される処理
     */
    public function onParseProduct(Product $product) {
    }
}
