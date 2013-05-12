<?php

namespace UrbanTheory\JustFit\Model;

/**
 * Mサイズ、Lサイズなどのサイズ別の寸法情報。
 * 現在はZOZOTOWNのTシャツ専用
 */
class Size {
    
    const SHIRT_CHEST    = 'shirt.chest';     //身幅
    const SHIRT_SHOULDER = 'shirt.shoulder';  //肩幅
    const SHIRT_LENGTH   = 'shirt.length';    //着丈
    const SHIRT_SLEEVE   = 'shirt.sleeve';    //袖丈
    
    /**
     * サイズ名
     * @var string
     */
    private $name;
    
    /**
     * 寸法の名称をキーとした寸法情報。
     * 値は任意制度数値として扱うためstring。
     * @var array
     */
    private $data;
    
    
    public function __construct() {
        $this->init();
    }
    
    public function init() {
        $this->name = '';
        $this->data = array();
    }
    
    public function getName() {
        return $this->name;
    }
    
    
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function setValue($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }

    public function getValue($key, $default='0') {
        return (isset($this->data[$key])) ? $this->data[$key] : $default;
    }
    
    public function hasKey($key) {
        return isset($this->data[$key]);
    }
    
    public function getAllData() {
        return $this->data;
    }
}

