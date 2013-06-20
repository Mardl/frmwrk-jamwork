<?php

namespace unittest\jamwork\form;

use \jamwork\form\FileField;

class FileFieldTest extends \PHPUnit_Framework_TestCase
{
	public function testType()
	{
		$type = $this->filefield->getType();
		$this->assertSame('file', $type);
	}
	
	
	protected function setUp()
	{
		$this->filefield = new FileField();
	}
	
	protected function tearDown()
	{
		unset($this->filefield);
	} 
}
