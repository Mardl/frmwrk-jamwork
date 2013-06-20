<?php

namespace unittest\jamwork\controller;

use \jamwork\controller\DateTimeConverter;

class DateTimeConverterTest extends \PHPUnit_Framework_TestCase
{
	
	private $converter = null;
	private $converterFallback = null;
	
	public function testSetOffset()
	{
		$this->assertEmpty($this->readAttribute($this->converter, 'offset'));	
			
		$this->converter->setOffset(2);
		$this->assertSame(2, $this->readAttribute($this->converter, 'offset'));	
	}
	
	public function testAddFallback()
	{
		$fallbacktime = $this->readAttribute($this->converterFallback, 'fallback');
			
		$this->converterFallback->addFallback(2);
		$this->assertSame($fallbacktime+2, $this->readAttribute($this->converterFallback, 'fallback'));

		$this->converterFallback->addFallback(3);
		$this->assertSame($fallbacktime+2+3, $this->readAttribute($this->converterFallback, 'fallback'));

	}	
		
	public function testGet()
	{
		$this->assertSame('22.08.2010 - 17:16:15', $this->converter->get('d.m.Y - H:i:s'));
		$this->assertSame('08-22-10 - 05:16:15', $this->converter->get('m-d-y - h:i:s'));	
		
		$this->converter->setOffset(2);
		$this->assertSame('08-22-10 - 05:16:17', $this->converter->get('m-d-y - h:i:s'));		
	}
	
	public function testAnalyze()
	{
		//siehe testGet
	}
	
	public function testAnalyzeDate()
	{
		//siehe testGet
	}
	
	public function testExplode()
	{
		//siehe testGet
	}
	
	public function testAnalyzeTime()
	{
		//siehe testGet
	}
	
	public function testGetTimestamp()
	{
		//siehe testGet
	}	
		
	protected function setUp()
	{
		$this->converter = new DateTimeConverter('2010-8-22 17:16:15');
		$this->converterFallback = new DateTimeConverter('');
	}
	
	protected function tearDown()
	{
		unset($this->converter);
	}
}
