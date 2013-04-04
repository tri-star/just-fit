<?php

namespace UrbanTheory\JustFit\Engine;

use TestLib\BaseTestCase;
use UrbanTheory\JustFit\Model\Size;

class ResultPageParserTest extends BaseTestCase {
    
    private $fixtureDir;
    
    public function setUp() {
        $this->fixtureDir = __DIR__ . '/fixtures';
    }
    
    public function test検索結果の件数と解析結果を取得出来ること() {
        $html = file_get_contents($this->fixtureDir . '/NormalExtraction.html');
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'ASCII, JIS, UTF-8, EUC-JP, SJIS');
        
        $extractEngine = new ResultPageParser();
        $resultCount = $extractEngine->extractProductCount($html);
        $this->assertEquals('9738', $resultCount);
        
        $resultData = $extractEngine->extractProductUrls($html);
        
        $this->assertCount(30, $resultData);
    }
    
}
