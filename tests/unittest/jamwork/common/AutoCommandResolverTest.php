<?php

namespace unittest\jamwork\common;

use \jamwork\common\AutoCommandResolver;
use \jamwork\common\Registry;


class AutoCommandResolverTest extends \PHPUnit_Framework_TestCase
{
	private $factory = null;
	
	public function testRun()
	{
		$this->eventDispatcher->expects($this->exactly(3))
             ->method('triggerEvent')
             ->will($this->returnValue($this->event));	
		
		$this->event->expects($this->exactly(3))
             ->method('isCanceled')
             ->will($this->returnValue(false));		
			
		$this->factory->expects($this->exactly(3))->method('addCommand');
		$this->resolver->run('unittest/module/');
	}
	
	public function testGetCommandArray()
	{
		$method = new \ReflectionMethod($this->resolver, 'getCommandArray');
		$method->setAccessible(true);
		
		$ret = $method->invoke($this->resolver, 'unittest/module/');
		
		
		//muss einzeln getestet werden, da die Reihenfolge des Array´s variieren kann
		
		$this->assertTrue(isset($ret['Testmodul1Command.php']));
		$this->assertTrue(isset($ret['TestmodulnonameCommand.php']));
		$this->assertTrue(isset($ret['TestmodulnoversionCommand.php']));
		
		$this->assertSame($ret['Testmodul1Command.php'],'unittest\module\testmodul1\Testmodul1');
		$this->assertSame($ret['TestmodulnonameCommand.php'],'unittest\module\testmodulnoname\Testmodulnoname');
		$this->assertSame($ret['TestmodulnoversionCommand.php'],'unittest\module\testmodulnoversion\Testmodulnoversion');
	}
		
	protected function setUp()
	{
		$registry = Registry::getInstance();
		$this->eventDispatcher = $this->getMockBuilder('\jamwork\common\EventDispatcher')
                     	   ->disableOriginalConstructor()
                     	   ->getMock();
		$this->event = $this->getMockBuilder('\jamwork\common\Event')
                     	   ->disableOriginalConstructor()
                     	   ->getMock();				   
						   
						   
		$registry->setEventDispatcher($this->eventDispatcher);	
			
			
		$this->factory = $this->getMock('jamwork\common\CommandFactory');
		$basepath = dirname(__FILE__).'/../../../';	
		$this->resolver = new AutoCommandResolver($this->factory, $basepath);
	}
	
	protected function tearDown()
	{
		unset($this->resolver);
		unset($this->factory);
	}
}
