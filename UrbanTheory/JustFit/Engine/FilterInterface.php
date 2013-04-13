<?php


namespace UrbanTheory\JustFit\Engine;

use UrbanTheory\JustFit\Model\Product;


interface FilterInterface {
    
    /**
     * 
     * @param Product $product
     * @return boolean 条件にマッチしたかどうか
     */
    public function isMatch(Product $product);
    
}
