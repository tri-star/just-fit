<?php

namespace UrbanTheory\JustFit\Engine;


use UrbanTheory\JustFit\Model\Product;
/**
 * 複数のFilterInterfaceを連結して判定を行うクラス。
 * 現時点ではひたすら末尾に連結するのみ
 */
class FilterChain {
    
    /**
     * Filterの一覧。
     * @var array
     */
    private $filters;
    
    public function __construct() {
        $this->filters = array();
    }
    
    public function addFilter(FilterInterface $filter) {
        $this->filters[] = $filter;
    }
    
    public function isMatch(Product $product) {
        foreach($this->filters as $filter) {
            if(!$filter->isMatch($product)) {
                return false;
            }
        }
        return true;
    }
    
}


