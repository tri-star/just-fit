<?php


namespace UrbanTheory\JustFit\Engine;


use TestLib\BaseTestCase;
use UrbanTheory\JustFit\Model\Product;
use UrbanTheory\JustFit\Model\Size;
use UrbanTheory\JustFit\Model\Condition;


class TshirtSizeFilterTest extends BaseTestCase {
    
    public function testSingleMatch() {
        //サイズ情報が1つしかない商品に対する判定のテスト。
        $size = new Size();
        $size->setName('M')
            ->setValue(Size::SHIRT_LENGTH, '63.5')
            ->setValue(Size::SHIRT_SHOULDER, '42.0')
            ->setValue(Size::SHIRT_CHEST, '90.0')
            ->setValue(Size::SHIRT_SLEEVE, '20.0');
        
        $product = new Product();
        $product->getSizeCollection()->add($size);
        
        $filter = new TshirtSizeFilter();
        
        //条件をセットせずにisMatchを呼び出そうとした場合trueが返る。
        $this->assertTrue($filter->isMatch($product));
        
        $condition = new Condition();
        
        //空の条件でisMatchを呼び出した場合、trueが返る
        $filter->setCondition($condition);
        $this->assertTrue($filter->isMatch($product));
        
        //全ての条件を指定しており、それが条件を満たす内容であれば
        //isMatch()はtrueを返す。
        $condition->set('tshirt_length_min', '10.0');
        $condition->set('tshirt_length_max', '100.0');
        $condition->set('tshirt_shoulder_min', '20.0');
        $condition->set('tshirt_shoulder_max', '125.0');
        $condition->set('tshirt_chest_min', '30.0');
        $condition->set('tshirt_chest_max', '135.0');
        $condition->set('tshirt_sleeve_min', '0.0');
        $condition->set('tshirt_sleeve_max', '50.0');
        
        $this->assertTrue($filter->isMatch($product));
    }
    
    public function testNotMatch() {
        //条件のうち1つがマッチしない場合、isMatchがfalseを返すことを確認する。
        $size = new Size();
        $size->setName('M')
            ->setValue(Size::SHIRT_LENGTH, '63.5')
            ->setValue(Size::SHIRT_SHOULDER, '42.0')
            ->setValue(Size::SHIRT_CHEST, '90.0')
            ->setValue(Size::SHIRT_SLEEVE, '20.0');
        
        $product = new Product();
        $product->getSizeCollection()->add($size);
        
        $filter = new TshirtSizeFilter();
        
        $condition = new Condition();
        
        $filter->setCondition($condition);
        
        //tshirt_length_maxが条件を満たしていない場合
        //isMatch()はfalseを返す。
        $condition->set('tshirt_length_min', '10.0');
        $condition->set('tshirt_length_max', '15.0');
        $condition->set('tshirt_shoulder_min', '20.0');
        $condition->set('tshirt_shoulder_max', '125.0');
        $condition->set('tshirt_chest_min', '30.0');
        $condition->set('tshirt_chest_max', '135.0');
        $condition->set('tshirt_sleeve_min', '0.0');
        $condition->set('tshirt_sleeve_max', '50.0');
        
        $this->assertFalse($filter->isMatch($product));
        
    }
    
    public function testMultiMatch() {
        //Mサイズ、Lサイズなど複数のサイズに対する判定のテスト。
        $sizeM = new Size();
        $sizeM->setName('M')
        ->setValue(Size::SHIRT_LENGTH, '63.5')
        ->setValue(Size::SHIRT_SHOULDER, '42.0')
        ->setValue(Size::SHIRT_CHEST, '90.0')
        ->setValue(Size::SHIRT_SLEEVE, '20.0');
        
        $sizeL = new Size();
        $sizeL->setName('L')
            ->setValue(Size::SHIRT_LENGTH, '65.5')
            ->setValue(Size::SHIRT_SHOULDER, '43.0')
            ->setValue(Size::SHIRT_CHEST, '97.0')
            ->setValue(Size::SHIRT_SLEEVE, '23.0');
        
        $product = new Product();
        $product->getSizeCollection()->add($sizeM);
        $product->getSizeCollection()->add($sizeL);
        
        $filter = new TshirtSizeFilter();
        $condition = new Condition();
        $filter->setCondition($condition);
        
        //全ての条件を指定しており、それが条件を満たす内容であれば
        //isMatch()はtrueを返す。
         $condition->set('tshirt_length_min', '60.5');
        $condition->set('tshirt_length_max', '63.5');
        $condition->set('tshirt_shoulder_min', '40.0');
        $condition->set('tshirt_shoulder_max', '42.0');
        $condition->set('tshirt_chest_min', '89.0');
        $condition->set('tshirt_chest_max', '90.0');
        $condition->set('tshirt_sleeve_min', '19.0');
        $condition->set('tshirt_sleeve_max', '20.5');
        
        $this->assertTrue($filter->isMatch($product));
    }
    
    public function testBoundary() {
        $sizeM = new Size();
        $sizeM->setName('M')
        ->setValue(Size::SHIRT_LENGTH, '63.5')
        ->setValue(Size::SHIRT_SHOULDER, '42.0')
        ->setValue(Size::SHIRT_CHEST, '90.0')
        ->setValue(Size::SHIRT_SLEEVE, '20.0');
        
        $product = new Product();
        $product->getSizeCollection()->add($sizeM);
        
        $filter = new TshirtSizeFilter();
        $condition = new Condition();
        $filter->setCondition($condition);
        
        //商品と完全に一致する条件の場合、trueが返る
        $condition->set('tshirt_length_min', '63.5');
        $condition->set('tshirt_length_max', '63.5');
        $condition->set('tshirt_shoulder_min', '42.0');
        $condition->set('tshirt_shoulder_max', '42.0');
        $condition->set('tshirt_chest_min', '90.0');
        $condition->set('tshirt_chest_max', '90.0');
        $condition->set('tshirt_sleeve_min', '20.0');
        $condition->set('tshirt_sleeve_max', '20.0');
        
        $this->assertTrue($filter->isMatch($product));
        
        //最小値だけを指定して、条件を満たすとtrueが返ることを確認
        $condition->init();
        $condition->set('tshirt_length_min', '63.4');
        $this->assertTrue($filter->isMatch($product));
        
        //最小値だけを指定して、条件を満たさないとfalseが返ることを確認
        $condition->init();
        $condition->set('tshirt_length_min', '63.6');
        $this->assertFalse($filter->isMatch($product));
        
        //最大値だけを指定して、条件を満たすとtrueが返ることを確認
        $condition->init();
        $condition->set('tshirt_length_max', '63.6');
        $this->assertTrue($filter->isMatch($product));
        
        //最大値だけを指定して、条件を満たさないとfalseが返ることを確認
        $condition->init();
        $condition->set('tshirt_length_max', '63.4');
        $this->assertFalse($filter->isMatch($product));
    }
    
    //少数出ない場合、数値を渡した場合...
}
