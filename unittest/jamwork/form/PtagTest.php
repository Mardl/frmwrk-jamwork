<?php

namespace unittest\jamwork\form;

use \jamwork\form\Ptag;

class PtagTest extends \PHPUnit_Framework_TestCase
{
	public function testAllTests()
	{
		//siehe testTextarea
	}
	
	public function testGetFieldType()
	{
		$this->assertSame('p', $this->ptag->getFieldType());
	}
	
	protected function setUp()
	{
		$this->ptag = new Ptag();
	}
	
	protected function tearDown()
	{
		unset($this->ptag);
	} 
}
