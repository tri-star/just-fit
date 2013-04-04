<?php

namespace UrbanTheory\JustFit\Engine;


use TestLib\BaseTestCase;
use UrbanTheory\JustFit\Model\Condition;


class UrlBuilderTest extends BaseTestCase {
    
    
    public function testSleeveStyle() {
        
        $builder = new UrlBuilderForTest();
        $result = $builder->getSleeveStyleValuesForTest(array(Condition::SLEEVE_STYLE_SHORT));
        $this->assertCount(1, $result);
        $this->assertEquals('5493_21158', $result[0]);
        
        $result = $builder->getSleeveStyleValuesForTest(array(Condition::SLEEVE_STYLE_LONG));
        $this->assertCount(1, $result);
        $this->assertEquals('5493_21159', $result[0]);
        
        $result = $builder->getSleeveStyleValuesForTest(array());
        $this->assertCount(0, $result);
        
        $result = $builder->getSleeveStyleValuesForTest('');
        $this->assertCount(0, $result);
        
        $result = $builder->getSleeveStyleValuesForTest(null);
        $this->assertCount(0, $result);
    }
    
}



class UrlBuilderForTest extends UrlBuilder {
    
    public function getSleeveStyleValuesForTest($input) {
        return $this->getSleeveStyleValues($input);
    }
    
}