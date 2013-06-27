<?php

namespace unittest\jamwork\form;

use jamwork\form\Password;

class PasswordTest extends \PHPUnit_Framework_TestCase
{

	private $checkbox = false;

	public function testType()
	{
		$type = $this->pwd->getType();
		$this->assertSame('password', $type);
	}

	protected function setUp()
	{
		$this->pwd = new Password();
	}

	public function testGetFieldType()
	{
		$this->assertSame('password', $this->pwd->getFieldType());
	}

	protected function tearDown()
	{
		unset($this->pwd);
	}

}