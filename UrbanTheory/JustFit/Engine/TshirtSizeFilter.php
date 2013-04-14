<?php

namespace UrbanTheory\JustFit\Engine;


use UrbanTheory\JustFit\Model\Size;
use UrbanTheory\JustFit\Model\Product;

class TshirtSizeFilter extends SizeFilterBase {
    
    public function isMatch(Product $product) {
        
        if(!is_object($this->condition)) {
            //条件が全く指定されていない場合、現在はtrueを返す。
            return true;
        }
        
        $lengthMin = $this->condition->get('tshirt_length_min', null);
        $lengthMax = $this->condition->get('tshirt_length_max', null);
        $shoulderMin = $this->condition->get('tshirt_shoulder_min', null);
        $shoulderMax = $this->condition->get('tshirt_shoulder_max', null);
        $chestMin = $this->condition->get('tshirt_chest_min', null);
        $chestMax = $this->condition->get('tshirt_chest_max', null);
        $sleeveMin = $this->condition->get('tshirt_sleeve_min', null);
        $sleeveMax = $this->condition->get('tshirt_sleeve_max', null);
        
        $match = false;
        foreach($product->getSizeCollection()->getAll() as $size) {
            //着丈
            if(!$this->isValueInRange($lengthMin, $lengthMax, $size->getValue(Size::SHIRT_LENGTH))) {
                continue;
            }
            
            //肩幅
            if(!$this->isValueInRange($shoulderMin, $shoulderMax, $size->getValue(Size::SHIRT_SHOULDER))) {
                continue;
            }
            
            //身幅
            if(!$this->isValueInRange($chestMin, $chestMax, $size->getValue(Size::SHIRT_CHEST))) {
                continue;
            }
            
            //袖丈
            if(!$this->isValueInRange($sleeveMin, $sleeveMax, $size->getValue(Size::SHIRT_SLEEVE))) {
                continue;
            }
            
            $match = true;
            break;
        }
        
        return $match;
    }
    
}
