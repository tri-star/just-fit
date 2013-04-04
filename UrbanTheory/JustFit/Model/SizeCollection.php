<?php

namespace UrbanTheory\JustFit\Model;



/**
 * Mサイズ、Lサイズなど各サイズの寸法情報の一覧を保持する。
 */
class SizeCollection {
    
    /**
     * Sizeの配列。
     * @var array
     */
    private $collection;
    
    
    public function __construct() {
        $this->init();
    }
    
    
    public function init() {
        $this->collection = array();
    }
    
    
    public function add(Size $item) {
        $this->collection[] = $item;
    }
    
    public function getAll() {
        return $this->collection;
    }
    
    public function getCount() {
        return count($this->collection);
    }
    
    public function get($index) {
        if(!isset($this->collection[$index])) {
            return null;
        }
        return $this->collection[$index];
    }
    
}

