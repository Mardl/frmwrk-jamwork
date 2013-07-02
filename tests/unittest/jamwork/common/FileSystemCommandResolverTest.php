<?php

namespace unittest\jamwork\common;

use jamwork\common\FileSystemCommandResolver;
use jamwork\common\HttpRequest;
use jamwork\common\Registry;

class FileSystemCommandResolverTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var FileSystemCommandResolver
	 */
	private $CommandResolver;

	public function testGetCommand_OhneCommand()
	{
		$request = new HttpRequest(array(), array(), array(), array());
		$ret = $this->CommandResolver->getCommand($request);
		$this->assertFalse($ret);
	}

	public function testGetCommand()
	{
		$this->eventDispatcher->expects($this->exactly(1))->method('triggerEvent')->will($this->returnValue($this->event));


		$this->event->expects($this->exactly(1))->method('isCanceled')->will($this->returnValue(false));

		$get['cmd'] = 'unittest\module\Unittest';
		$request = new HttpRequest($get, array(), array(), array());
		$ret = $this->CommandResolver->getCommand($request);
		$this->assertInstanceOf($get['cmd'] . 'Command', $ret);
	}

	public function testSetNoPermission()
	{
		$attr = $this->readAttribute($this->CommandResolver, 'noPermission');
		$this->assertEmpty($attr);

		$this->CommandResolver->setNoPermission('Foo');

		$attr = $this->readAttribute($this->CommandResolver, 'noPermission');
		$this->assertSame('Foo', $attr);
	}

	public function testLoadCommand()
	{

		$this->eventDispatcher->expects($this->exactly(1))->method('triggerEvent')->will($this->returnValue($this->event));


		$this->event->expects($this->exactly(1))->method('isCanceled')->will($this->returnValue(false));

		$method = new \ReflectionMethod($this->CommandResolver, 'loadCommand');
		$method->setAccessible(true);

		$command = $method->invoke($this->CommandResolver, '\unittest\module\Unittest');
		$this->assertInstanceOf('\unittest\module\UnittestCommand', $command);
	}

	public function testGetCommandInstance()
	{
		$this->eventDispatcher->expects($this->exactly(1))->method('triggerEvent')->will($this->returnValue($this->event));


		$this->event->expects($this->exactly(1))->method('isCanceled')->will($this->returnValue(false));

		$command = $this->CommandResolver->getCommandInstance('\unittest\module\Unittest');
		$this->assertInstanceOf('\unittest\module\UnittestCommand', $command);
	}

	public function testGetCommandInstance_Exception()
	{
		try
		{
			$command = $this->CommandResolver->getCommandInstance('\unittest\module\GibstNicht');
		} catch (\Exception $e)
		{
			return;
		}

		$this->Fail("Exception erwartet");
	}


	protected function setUp()
	{
		$registry = Registry::getInstance();
		$this->eventDispatcher = $this->getMockBuilder('\jamwork\common\EventDispatcher')->disableOriginalConstructor()->getMock();
		$this->event = $this->getMockBuilder('\jamwork\common\Event')->disableOriginalConstructor()->getMock();


		$registry->setEventDispatcher($this->eventDispatcher);

		$this->CommandResolver = new FileSystemCommandResolver();
	}

	protected function tearDown()
	{
		unset($this->CommandResolver);
	}
}