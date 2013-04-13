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

        //テスト用に初期化されたSearchEngineを取得する。
        $searchEngine = $this->getPerfectMockedEngine(5);
        $searchEngine->setOptions(array(
            SearchEngine::OPTION_WEIGHT_PER_PRODUCT => 0,
        ));
        
        //検索結果が5件として、SearchEventHandlerのそれぞれのメソッドが適切な回数呼び出されることを確認する。
        $handler = $this->getMock('UrbanTheory\JustFit\Engine\SearchEventHandler');
        $handler->expects($this->once())->method('onSearchStart')->with($this->equalTo(5), $this->equalTo(1));
        $handler->expects($this->once())->method('onParseResultPageStart')->with($this->equalTo(1));
        $handler->expects($this->exactly(5))->method('onParseProductStart');
        $handler->expects($this->exactly(5))->method('onParseProduct');
        
        $searchEngine->addEventHandler($handler);
        $condition = new Condition();
        $searchEngine->search($condition);
    }
    
    
    /**
     * 複数ページを解析するテスト
     */
    public function testPaginate() {
        //テスト用に初期化されたSearchEngineを取得する。
        $searchEngine = $this->getPerfectMockedEngine(90);
        $searchEngine->setOptions(array(
                SearchEngine::OPTION_WEIGHT_PER_PRODUCT => 0,
        ));
        
        //検索結果が90件(Mockを作成する都合上30の倍数)として、SearchEventHandlerのそれぞれのメソッドが適切な回数呼び出されることを確認する。
        $handler = $this->getMock('UrbanTheory\JustFit\Engine\SearchEventHandler');
        $handler->expects($this->once())->method('onSearchStart')->with($this->equalTo(90), $this->equalTo(3));
        $handler->expects($this->exactly(3))->method('onParseResultPageStart');
        $handler->expects($this->exactly(90))->method('onParseProductStart');
        $handler->expects($this->exactly(90))->method('onParseProduct');
        
        $searchEngine->addEventHandler($handler);
        $condition = new Condition();
        $searchEngine->search($condition);
    }
    
    /**
     * HTTPリクエストを完全に発生させないSearchEngineオブジェクトを返す。
     * @param int $productCount 検索結果のヒット数
     * @return \UrbanTheory\JustFit\Engine\SearchEngine
     */
    private function getPerfectMockedEngine($productCount) {
        //テスト用のレスポンスを返すClientのStubを作成。
        $response = new Response('Dummy Text');
        $clientStub = $this->getMockBuilder('Goutte\Client')->getMock();
        $clientStub->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        
        //ResultPageParserのStubを作成。
        $itemsPerPage = min($productCount, 30);
        $resultPageParserStub = $this->getMock('UrbanTheory\JustFit\Engine\ResultPageParser');
        $resultPageParserStub->expects($this->any())->method('extractProductCount')->will($this->returnValue($productCount));
        $resultPageParserStub->expects($this->any())->method('extractProductUrls')->will($this->returnValue(range(1, $itemsPerPage)));
        
        //ProductPageParserのStubを作成。
        $productPageParserStub = $this->getMock('UrbanTheory\JustFit\Engine\ProductPageParser');
        $productPageParserStub->expects($this->any())->method('extract')->will($this->returnValue(new Product()));
        
        $searchEngine = new SearchEngine($resultPageParserStub, $productPageParserStub, $clientStub);
        $searchEngine->setOptions(array(
            SearchEngine::OPTION_WEIGHT_PER_PRODUCT => 0,
        ));
        
        return $searchEngine;
    }
}


