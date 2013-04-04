<?php

namespace TestLib;

/**
 * 全てのテストケースに共通の処理を定義する。
 */
class BaseTestCase extends \PHPUnit_Framework_TestCase {
    
    public function getFixtureRoot() {
        return dirname(dirname(__FILE__)) . '/fixtures';
    }
    
}
