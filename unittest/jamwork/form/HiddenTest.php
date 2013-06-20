<?php

namespace unittest\jamwork\form;

use jamwork\form\Hidden;

class hiddenTest extends \PHPUnit_Framework_TestCase
{
	private $checkbox = false;
	
	public function testType()
	{
		$type = $this->hidden->getType();
		$this->assertSame('hidden', $type);
	}
	
	public function testGetFieldType()
	{
		$this->assertSame('hidden', $this->hidden->getFieldType());
	}
	
	protected function setUp()
	{
		$this->hidden = new Hidden();
	}
	
	protected function tearDown()
	{
		unset($this->hidden);
	} 
	
}