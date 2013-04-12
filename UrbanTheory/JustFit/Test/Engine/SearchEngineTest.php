<?php


namespace UrbanTheory\JustFit\Engine;


use TestLib\BaseTestCase;
use Goutte\Client;
use Symfony\Component\BrowserKit\Response;
use UrbanTheory\JustFit\Model\Condition;
use UrbanTheory\JustFit\Model\Product;


class SearchEngineTest extends BaseTestCase {
    
    protected $fixtureDir;
    
    public function setUp() {
        $this->fixtureDir = __DIR__ . '/fixtures';
    }
    
    public function testSearch() {

        //テスト用のレスポンスを返すClientのStubを作成。
        $response = new Response('Dummy Text');
        $clientStub = $this->getMockBuilder('Goutte\Client')->getMock();
        $clientStub->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        
        //ResultPageParserのStubを作成。
        $resultPageParserStub = $this->getMock('UrbanTheory\JustFit\Engine\ResultPageParser');
        $resultPageParserStub->expects($this->any())->method('extractProductCount')->will($this->returnValue(5));
        $resultPageParserStub->expects($this->any())->method('extractProductUrls')->will($this->returnValue(array(1,2,3,4,5)));
        
        //ProductPageParserのStubを作成。
        $productPageParserStub = $this->getMock('UrbanTheory\JustFit\Engine\ProductPageParser');
        $productPageParserStub->expects($this->any())->method('extract')->will($this->returnValue(new Product()));
        
        $searchEngine = new SearchEngine($resultPageParserStub, $productPageParserStub, $clientStub);
        $searchEngine->setOptions(array(
            SearchEngine::OPTION_WEIGHT_PER_PRODUCT => 0,
            SearchEngine::OPTION_MAX_PAGES => 1,
        ));
        
        $handler = $this->getMock('UrbanTheory\JustFit\Engine\SearchEventHandler');
        $handler->expects($this->once())->method('onSearchStart')->with($this->equalTo(5), $this->equalTo(1));
        $handler->expects($this->once())->method('onParseResultPageStart');
        $handler->expects($this->exactly(5))->method('onParseProductStart');
        $handler->expects($this->exactly(5))->method('onParseProduct');
        
        $searchEngine->addEventHandler($handler);
        $condition = new Condition();
        $searchEngine->search($condition);
    }
}


