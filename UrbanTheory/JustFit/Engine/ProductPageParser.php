<?php

namespace UrbanTheory\JustFit\Engine;


use Symfony\Component\DomCrawler\Crawler;
use UrbanTheory\JustFit\Model\Product;
use UrbanTheory\JustFit\Model\Size;


/**
 * ZOZOTOWNの商品詳細ページから各種情報を抽出する
 */
class ProductPageParser {
    
    public function __construct() {
    }
    
    public function extract($html) {
        
        $product = new Product();
        
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'ASCII, JIS, UTF-8, EUC-JP, SJIS');
        
        $crawler = new Crawler($html);
        $product->setBrandName($this->getBrandName($crawler));
        $product->setName($this->getProductName($crawler));
        $priceData = $this->getPrice($crawler);
        $product->setPrice($priceData['price']);
        $product->setBasePrice($priceData['base_price']);
        $product->setUrl($this->getProductUrl($crawler));
        $product->setImageUrl($this->getImageUrl($crawler));
        $product->setProductCode($this->getProductCode( $product->getUrl() ));
        $product->setContactNo($this->getContactNo( $product->getUrl() ));
        
        $sizes = $this->getSizeInfo($crawler);
        foreach($sizes as $size) {
            $product->getsizeCollection()->add($size);
        }
        return $product;
    }
    
    private function getProductCode($productUrl) {
        //商品URLから抽出する。
        $matches = array();
        if(!preg_match('/gid=([0-9]+)/i', $productUrl, $matches)) {
            return '';
        }
        //URL中のgidで取得できる番号が商品コード。
        return $matches[1];
    }
    
    private function getContactNo($productUrl) {
        //商品コードの先頭の番号を+1すると問い合わせ番号になる模様。
        $productCode = $this->getProductCode($productUrl);
        $headDigit = (int)substr($productCode, 0, 1);
        $headDigit++;
        return $headDigit . substr($productCode, 1);
    }
    
    private function getBrandName(Crawler $crawler) {
        $crawler = $crawler->filterXPath('//ul[@id="nameList"]/li/a');
        $names = array();
        foreach($crawler as $node) {
            $name = html_entity_decode($node->nodeValue, ENT_COMPAT, 'Shift_JIS');
            if($name != '') {
                $names[] = $name;
            }
        }
        return $names;
    }
    
    private function getProductName(Crawler $crawler) {
        //$crawler = $crawler->filterXPath('//div[@class="infoBlock"]/h2');
        $crawler = $crawler->filterXPath('//div[@id="item-intro"]/h2');
        
        $name = html_entity_decode($crawler->text(), ENT_COMPAT, 'Shift_JIS');
        return $name;
    }
    
    private function getProductUrl(Crawler $crawler) {
        $crawler = $crawler->filterXPath('//link[@rel="canonical"]/@href');
        return $crawler->text();
    }
    
    private function getPrice(Crawler $crawler) {
        $c = $crawler->filterXPath('//p[@class="price priceDown"]/span');
        if($c->count() > 0) {
            $price = $this->numberize($c->text());
            return array('price' => $price, 'base_price' => $price);
        }
        $c = $crawler->filterXPath('//p[@class="price"]/span');
        if($c->count() > 0) {
            $price = $this->numberize($c->text());
            return array('price' => $price, 'base_price' => $price);
        }
        $c = $crawler->filterXPath('//p[@class="price"]');
        if($c->count() > 0) {
            $price = $this->numberize($c->text());
            return array('price' => $price, 'base_price' => $price);
        }
        return array('price' => 0, 'base_price' => 0);
    }
    
    private function getImageUrl($crawler) {
        $crawler = $crawler->filterXPath('//div[@id="photoMain"]/img/@src');
        $url = $crawler->text();
        $url = str_replace('_500.jpg', '_125.jpg', $url);
        return $url;
    }
    
    private function getSizeInfo($crawler) {
        $sizeBlock = $crawler->filterXPath('//div[@class="sizeBlock"]/*/div[@class="contbox"]/table');
        $sizeMetaNameBlock = $sizeBlock->filterXpath('//thead/tr/th');
    
        $keys = array();
        foreach($sizeMetaNameBlock as $idx=>$node) {
            $keys[$idx] = $node->nodeValue;
        }
        
        $columnCount = count($keys);
        $sizeDataRows = $sizeBlock->filterXpath('//tbody/tr');
        $sizes = array();
        foreach($sizeDataRows as $idx=>$row) {
            $size = new Size();
            $header = $row->firstChild;
            $size->setName(trim($header->nodeValue));
            
            for($td=$header->nextSibling, $colNo=1; $td != null; $td = $td->nextSibling) {
                
                if($td->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $key = $this->getSizeKeyFromName($keys[$colNo]);
                $size->setValue($key, trim($td->nodeValue));
                $colNo++;
            }
            $sizes[] = $size;
        }
        return $sizes;
    }
    
    private function getSizeKeyFromName($name) {
        switch($name) {
            case '着丈': return Size::SHIRT_LENGTH;
            case '肩幅': return Size::SHIRT_SHOULDER;
            case '身幅': return Size::SHIRT_CHEST;
            case 'そで丈': return Size::SHIRT_SLEEVE;
        }
        return '-';
    }
    
    private function createSizeData($data) {
        //標準パターンの場合
        $map = array(
            array('from'=>'着丈', 'to'=>Size::SHIRT_LENGTH),
            array('from'=>'肩幅', 'to'=>Size::SHIRT_SHOULDER),
            array('from'=>'身幅', 'to'=>Size::SHIRT_CHEST),
            array('from'=>'そで丈', 'to'=>Size::SHIRT_SLEEVE),
        );
        $size = new Size();
        
        $size->setName($data['']);
        foreach($map as $condition) {
            if(isset($data[$condition['from']])) {
                $size->setValue($condition['to'], $value);
            }
        }
        return $size;
    }
    
    private function numberize($string) {
        return str_replace(array('\\', '¥', ',','→', '￥'), array('', '', '', ''), $string);
    }
    
}
