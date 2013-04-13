<?php

namespace UrbanTheory\JustFit\Engine;

use UrbanTheory\JustFit\Model\Product;
use UrbanTheory\JustFit\Model\Condition;

abstract class SizeFilterBase implements FilterInterface {
    /**
     * @var Condition
     */
    protected $condition;
    
    public function __construct() {
        $this->condition = null;
    }
    
    public function setCondition(Condition $condition) {
        $this->condition = $condition;
    }
    
    abstract public function isMatch(Product $product);
    
    
    protected function isValueInRange($min, $max, $value) {
        if($min != null && bccomp($min, $value, 2) == 1) {
            return false;
        }
        if($max != null && bccomp($max, $value, 2) == -1) {
            return false;
        }
        return true;
    }
}
