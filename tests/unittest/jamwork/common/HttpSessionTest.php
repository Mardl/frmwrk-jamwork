<?php

namespace unittest\jamwork\common;

use \jamwork\common\HttpSession;

class HttpSessionTest extends \PHPUnit_Framework_TestCase
{

	public function testGetId()
	{
		$ses = $this->session->getId();
		$this->assertSame(session_id(), $ses);
	}

	public function testConstruct()
	{
		$this->session = $this->getMockBuilder('\jamwork\common\HttpSession')->setMethods(array('destroy'))->disableOriginalConstructor()->getMock();

		$ses = $this->session->getId();
		$this->assertSame(session_id(), $ses);
	}

	public function testGet()
	{
		$name = 'foo';
		$value = 'oof';

		$attr = $this->session->get($name);
		$this->assertEmpty($attr);

		$this->session->set($name, $value);
		$attr = $this->session->get($name);
		$this->assertSame($value, $attr);
	}

	public function testDelete()
	{
		$name = 'foo';
		$value = 'oof';

		$this->session->set($name, $value);
		$attr = $this->session->get($name);
		$this->assertSame($value, $attr);

		$this->session->delete($name);
		$attr = $this->session->get($name);
		$this->assertEmpty($attr);

	}

	public function testHas()
	{
		$name = 'foo';
		$value = 'oof';

		$attr = $this->session->has($name);
		$this->assertFalse($attr);

		$this->session->set($name, $value);
		$attr = $this->session->has($name);
		$this->assertTrue($attr);
	}

	public function testSet()
	{
		//siehe alle tests darüber
	}

	public function testDestroy()
	{
		//Vadim fragen
	}

	protected function setUp()
	{
		$this->session = $this->getMockBuilder('\jamwork\common\HttpSession')->setMethods(array('destroy'))->disableOriginalConstructor()->getMock();
	}

	protected function tearDown()
	{
		unset($this->session);
	}
}
