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
    
    public function testGetNumPage() {
        
        $fixturePath = $this->fixtureDir . '/SearchEngine_getNumPages.html';
        $response = new Response(file_get_contents($fixturePath));
        
        $clientStub = $this->getMockBuilder('Goutte\Client')->getMock();
        $clientStub->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        
        $searchEngine = new DummySearchEngine(null, null, $clientStub);
        $searchEngine->setOptions(array(SearchEngine::OPTION_WEIGHT_PER_PRODUCT => 0));
        
        $condition = new Condition();
        
        $test = $searchEngine->getNumPagesForTest($condition);
    }
    
    public function testSearch() {
        
        //テスト用のレスポンスを返すClientのStubを作成。
        $fixturePath = $this->fixtureDir . '/SearchEngine_search.html';
        $response = new Response(file_get_contents($fixturePath));
        
        $clientStub = $this->getMockBuilder('Goutte\Client')->getMock();
        $clientStub->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        
        //ProductPageParserのStubを作成。
        $productPageParserMock = $this->getMock('UrbanTheory\JustFit\Engine\ProductPageParser');
        $productPageParserMock->expects($this->any())->method('extract')->will($this->returnValue(new Product()));
        
        $searchEngine = new SearchEngine(null, $productPageParserMock, $clientStub);
        $searchEngine->setOptions(array(
            SearchEngine::OPTION_WEIGHT_PER_PRODUCT => 0,
            SearchEngine::OPTION_MAX_PAGES => 1,
        ));
        
        $handler = $this->getMock('UrbanTheory\JustFit\Engine\SearchEventHandler');
        $handler->expects($this->once())->method('onSearchStart')->with($this->equalTo(9738), $this->equalTo(1));
        $handler->expects($this->once())->method('onParseResultPageStart');
        //$handler->expects($this->exactly(30))->method('onParseProductStart');
        $handler->expects($this->exactly(30))->method('onParseProduct');
        
        $searchEngine->addEventHandler($handler);
        $condition = new Condition();
        $searchEngine->search($condition);
    }
}


class DummySearchEngine extends SearchEngine {
    
    public function __construct(ResultPageParser $resultPageParser=null, ProductPageParser $productPageParser=null, Client $client=null) {
        parent::__construct($resultPageParser, $productPageParser, $client);
    }
    
    public function getNumPagesForTest(Condition $condition) {
        return $this->getNumPages($condition);
    }
    
}


