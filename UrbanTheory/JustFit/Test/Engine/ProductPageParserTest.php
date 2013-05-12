<?php

namespace UrbanTheory\JustFit\Engine;


use TestLib\BaseTestCase;


class ProductPageParsertest extends BaseTestCase {
    
    public function setUp() {
        $this->fixtureDir = __DIR__ . '/fixtures';
        
    }
    
    /**
     * 標準的な商品ページの解析が出来ることを確認するテスト
     */
    public function testNormalUsage() {
        
        $html = file_get_contents($this->fixtureDir . '/ProductPageParser_Normal.html');
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'ASCII, JIS, UTF-8, EUC-JP, SJIS');
        
        
        $productPageParser = new ProductPageParser();
        
        $product = $productPageParser->extract($html);
        
        $this->assertEquals('リネンブレンドボーダーTee', $product->getName());
        //$this->assertEquals('', $product->getBrandName());
        $this->assertEquals('3990', $product->getBasePrice());
        $this->assertEquals('3990', $product->getPrice());
        $this->assertEquals('http://zozo.jp/shop/ciaopanictypy/goods.html?gid=2189047', $product->getUrl());
        $this->assertEquals('http://img5.zozo.jp/goodsimages/047/3189047/3189047_35_D_125.jpg', $product->getImageUrl());
        $this->assertEquals('2189047', $product->getProductCode());
        $this->assertEquals('3189047', $product->getContactNo());
        
    }
    
}
