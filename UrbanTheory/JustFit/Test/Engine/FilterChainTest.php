<?php

namespace UrbanTheory\JustFit\Engine;


use TestLib\BaseTestCase;
use UrbanTheory\JustFit\Model\Product;


class FilterChainTest extends BaseTestCase{
    
    public function testNoFilter() {
        //フィルターを何も登録していない場合は成功する。
        $filterChain = new FilterChain();
        $product = new Product();
        
        $this->assertTrue($filterChain->isMatch($product));
    }
    
    
    public function testTrue() {
        
        //Filterがtrueを返した場合、isMatchがtrueを返すことを確認。
        $filterChain = new FilterChain();
        $filterChain->addFilter(new TrueFilter());
        $product = new Product();
        
        $this->assertTrue($filterChain->isMatch($product));
    }
    
    
    public function testFalse() {
    
        //Filterがfalseを返した場合、isMatchがfalseを返すことを確認。
        $filterChain = new FilterChain();
        $filterChain->addFilter(new FalseFilter());
        $product = new Product();
    
        $this->assertFalse($filterChain->isMatch($product));
    }
    
    public function testTrueFalseCombination() {
    
        //Filterがtrueとfalseを返した場合、isMatchがfalseを返すことを確認。
        $filterChain = new FilterChain();
        $filterChain->addFilter(new TrueFilter());
        $filterChain->addFilter(new FalseFilter());
        $product = new Product();
    
        $this->assertFalse($filterChain->isMatch($product));
    }
    
}


class TrueFilter implements FilterInterface {
    
    public function isMatch(Product $product) {
        return true;
    }
    
}


class FalseFilter implements FilterInterface {

    public function isMatch(Product $product) {
        return false;
    }

}
