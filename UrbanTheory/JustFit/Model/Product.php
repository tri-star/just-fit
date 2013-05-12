<?php
namespace UrbanTheory\JustFit\Model;


/**
 * 商品の情報を保持するクラス
 */
class Product {
    
    private $productCode;
    
    private $name;
    
    private $brandName;
    
    private $url;
    
    private $price;
    
    private $basePrice;
    
    private $imageUrl;
    
    private $contactNo;
    
    /**
     * 
     * @var SizeCollection
     */
    private $sizeCollection;
    
    public function __construct() {
        $this->init();
    }
    
    public function init() {
        $this->productCode = '';
        $this->name = '';
        $this->brandName = array();
        $this->url = '';
        $this->price = 0;
        $this->basePrice = 0;
        $this->imageUrl = '';
        $this->contactNo = '';
        $this->sizeCollection = new SizeCollection();
    }
    
    public function getProductCode() {
        return $this->productCode;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getBrandName() {
        return $this->brandName;
    }
    
    public function getUrl() {
        return $this->url;
    }
    
    public function getImageUrl() {
        return $this->imageUrl;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getBasePrice() {
        return $this->basePrice;
    }
    
    /**
     * @return SizeCollection
     */
    public function getSizeCollection() {
        return $this->sizeCollection;
    }
    
    public function getContactNo() {
        return $this->contactNo;
    }
    
    
    public function setProductCode($code) {
        $this->productCode = $code;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function setBrandName($brandName) {
        $this->brandName = $brandName;
    }
    
    public function setUrl($url) {
        $this->url = $url;
    }

    public function setImageUrl($imageUrl) {
        $this->imageUrl = $imageUrl;
    }

    public function setPrice($price) {
        $this->price = $price;
    }
    
    public function setBasePrice($basePrice) {
        $this->basePrice = $basePrice;
    }
    
    public function setContactNo($contactNo) {
        $this->contactNo = $contactNo;
    }
    
}
