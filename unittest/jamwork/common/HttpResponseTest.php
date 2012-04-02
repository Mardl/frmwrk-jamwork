<?php

namespace unittest\jamwork\common;

use jamwork\common\HttpResponse;

class HttpResponseTest extends \PHPUnit_Framework_TestCase
{
	private $response;
	
	public function testGetStatus()
	{
		$status = $this->response->getStatus();
		$this->assertSame('200 OK', $status);
	}
	
	public function testSetStatus()
	{
		$sollStatus = 'Status1';
		$this->response->setStatus($sollStatus);
		
		$status = $this->response->getStatus();
		$this->assertSame($sollStatus, $status);
	}
	
	public function testAddGetHeader()
	{
		$sollKey = 'location';
		$sollHeader = array($sollKey => './unittest.php');
		
		$this->response->addHeader($sollKey, $sollHeader[$sollKey]);
		$headerArr = $this->response->getHeader();
		
		$this->assertSame($sollHeader, $headerArr);
	}
	
	public function testGetHeader()
	{
		// wird mit testAddGetHeader() getestet
	}
	
	public function testAddHeader()
	{
		// wird mit testAddGetHeader() getestet
	}
	
	public function testUnsetHeader()
	{
		$sollKey1 = 'location';
		$sollKey2 = 'location2';
		$sollHeader = array(
			$sollKey1 => './unittest.php', 
			$sollKey2 => './unittest2.php'
		);
		
		$this->response->addHeader($sollKey1, $sollHeader[$sollKey1]);
		$this->response->addHeader($sollKey2, $sollHeader[$sollKey2]);
		
		$this->response->unsetHeader($sollKey2);
		
		unset($sollHeader[$sollKey2]);
		
		$headerArr = $this->response->getHeader();
		
		$this->assertSame($sollHeader, $headerArr);
	}
	
	public function testSetGetBody()
	{
		$sollBody = 'Body1';
		$this->response->setBody($sollBody);
		
		$body = $this->response->getBody();
		
		$this->assertSame($sollBody, $body);
	}
	
	public function testSetBody()
	{
		// wird in testSetGetBody() getestet
	}
	
	public function testGetBody()
	{
		// wird in testSetGetBody() getestet
	}
	
	public function testWrite()
	{
		$sollBody = 'Body ';	
		$this->response->setBody($sollBody);
		$sollText = 'one hit to the body';
		$this->response->write($sollText);
		
		$text = $this->response->getBody();
		
		$this->assertSame($sollBody.$sollText, $text);
	}
	
	public function testAddReturn()
	{
		$obj = $this->getMock('unittest\module\UnittestCommand');
		$data = 'Das ist irgend ein Output...';
		
		$this->response->addReturn($obj, $data);
		
		$returns = $this->readAttribute($this->response, 'returns');
		$this->assertSame( $returns[ get_class($obj) ], $data );
	}
	
	public function testGetReturns()
	{
		$atr_returns = $this->readAttribute($this->response, 'returns');
		$get_returns = $this->response->getReturns();
		
		$this->assertEmpty( $atr_returns );
		$this->assertSame( $atr_returns, $get_returns );
		
		$obj = $this->getMock('unittest\module\UnittestCommand');
		$data = 'Das ist irgend ein Output...';
		$this->response->addReturn($obj, $data);
		
		$atr_returns = $this->readAttribute($this->response, 'returns');
		$get_returns = $this->response->getReturns();
		
		$this->assertSame( $atr_returns[ get_class($obj) ], $data );
		$this->assertSame( $atr_returns, $get_returns );
	}
	
	public function testFlush()
	{
		$sollKey1 = 'location';
		$sollKey2 = 'location2';
		$sollHeader = array(
			$sollKey1 => './unittest.php', 
			$sollKey2 => './unittest2.php'
		);
		
		$this->response->addHeader($sollKey1, $sollHeader[$sollKey1]);
		$this->response->addHeader($sollKey2, $sollHeader[$sollKey2]);
		
		$this->response->setStatus('302 Moved permanently');
		
		$this->response->write('[test]');
		$this->response->write('das ist ein test');
		$this->response->write('[/test]');
		
		$return = $this->response->flush(true);
		
		$this->assertContains('HEADER: HTTP/1.0 302 Moved permanently', $return);
		$this->assertContains('HEADER: location2: ./unittest2.php', $return);
		$this->assertContains('[test]das ist ein test[/test]', $return);
	}
	
	public function testFlushHeader()
	{
		// wird mit testFlush() getestet;
	}
	
	public function testFlushStatus()
	{
		// wird mit testFlush() getestet;
	}
	
	public function testDownloadFile_fileexists()
	{
		$response = $this->getMockBuilder('jamwork\common\HttpResponse')
                     	->disableOriginalConstructor()
						->setMethods(array('addHeader', 'flush', 'write'))
                     	->getMock();
						
		$response->expects($this->exactly(8))       
		        ->method('addHeader')
				->will($this->returnValue(true));
				
		$response->expects($this->exactly(1))       
		        ->method('write')
				->will($this->returnValue(true));
				
		$response->expects($this->exactly(0))       
		        ->method('flush')
				->will($this->returnValue(true));
		
		$response->downloadFile(__FILE__, true);
	}

	public function testDownloadFile_nofile()
	{
		$response = $this->getMockBuilder('jamwork\common\HttpResponse')
                     	->disableOriginalConstructor()
						->setMethods(array('addHeader', 'flush', 'write'))
                     	->getMock();
						
		$response->expects($this->exactly(4))       
		        ->method('addHeader')
				->will($this->returnValue(true));
				
		$response->expects($this->exactly(0))       
		        ->method('write')
				->will($this->returnValue(true));
				
		$response->expects($this->exactly(0))       
		        ->method('flush')
				->will($this->returnValue(true));
		try {
			$response->downloadFile('FOO', true);
        }
        catch ( \Exception $expected) {
            return;
        }
        $this->fail('An expected Exception has not been raised.');	
	}
	
	public function testHasHeader()
	{
		$this->response->addHeader('Content-Type', "application/force-download");
		$this->assertTrue($this->response->hasHeader('Content-Type'));
	}
	
	public function testHasHeader_false()
	{
		$this->response->unsetHeader('Content-Type');
		$this->assertFalse($this->response->hasHeader('Content-Type'));
	}
	
	protected function setUp()
	{
		$this->response = new HttpResponse();
	}
	
	protected function tearDown()
	{
		unset($this->response);
	}
}