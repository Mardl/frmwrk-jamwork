<?php

namespace unittest\jamwork\helper;

use \jamwork\helper\Uri;

class UriTest extends \PHPUnit_Framework_TestCase
{
	public function testSet()
	{
		$this->uri->set('FOO', 'BAR');	
		$attr = $this->readAttribute($this->uri, 'param');
		$this->assertSame($attr['FOO'], 'BAR');
	}	
		
	protected function setUp()
	{
		$this->request = $this->getMockBuilder('jamwork\common\HttpRequest')
                     	->disableOriginalConstructor()
                     	->getMock();
						
		$params = array('key1' => 'val1',
						'key2' => 'val2');
		
		$this->request->expects($this->exactly(1))       
		        ->method('getAllParameters')
				->will($this->returnValue($params));
							
		$this->uri = new Uri($this->request);
	}
	
	public function testRemove()
	{
		$this->uri->set('FOO', 'BAR');	
		$attr = $this->readAttribute($this->uri, 'param');
		$this->assertTrue(isset($attr['FOO']));
		
		$this->uri->remove('FOO');
		
		$attr = $this->readAttribute($this->uri, 'param');
		$this->assertTrue(!isset($attr['FOO']));
	}
	
	public function testIs()
	{
		$this->assertTrue($this->uri->is('key1'));	
		$this->assertFalse($this->uri->is('key3'));
	}
	
	protected function tearDown()
	{
		unset($this->request);
		unset($this->uri);
	}
	
	public function test__toString()
	{
		$str = $this->uri->__toString();
		$this->assertSame($str, '?key1=val1&key2=val2');	
	}
}
