<?php

namespace UrbanTheory\JustFit\Model;


/**
 * 検索条件を保持するオブジェクト。
 * 現在はZOZOTOWN専用。
 * 
 * TODO: 全ての条件を連想配列で持つのではなく、必須の条件はsetter/getterを設けてインターフェースを明確にした方が良いかもしれない。
 */
class Condition {
    
    private $data;
    
    const SLEEVE_STYLE_SHORT = 1;
    const SLEEVE_STYLE_LONG  = 2;
    
    const PRINT_STYLE_PLAIN     = 1;
    const PRINT_STYLE_BORDER    = 2;
    const PRINT_STYLE_STRIPE    = 3;
    const PRINT_STYLE_CHECK     = 4;
    const PRINT_STYLE_PRINT     = 5;
    const PRINT_STYLE_ONE_POINT = 6;
    const PRINT_STYLE_MISC      = 7;
    
    const NECK_STYLE_U     = 1;
    const NECK_STYLE_V     = 2;
    const NECK_STYLE_HENRY = 3;
    const NECK_STYLE_MISC  = 4;
    
    
    public function __construct() {
        $this->init(array());
    }
    
    public function init($data) {
        $this->data = $data;
    }
    
    public function get($key, $default=null) {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }
    
    public function set($key, $value) {
        $this->data[$key] = $value;
    }
    
    public function hasKey($key) {
        return isset($this->data[$key]);
    }
    
    public function hasValue($key) {
        if(!$this->hasKey($key)) {
            return false;
        }
        if($this->data[$key] === '') {
            return false;
        }
        if(is_array($this->data[$key]) && count($this->data[$key]) == 0) {
            return false;
        }
        return true;
    }
    
    
}

