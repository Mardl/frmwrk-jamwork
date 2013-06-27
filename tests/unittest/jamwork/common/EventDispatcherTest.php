<?php

namespace unittest\jamwork\common;

use jamwork\common\EventDispatcher;
use jamwork\common\Event;
use unittest\module\UnittestEventHandler;
use unittest\module\UnittestCancelEventHandler;


class EventDispatcherTest extends \PHPUnit_Framework_TestCase 
{
	protected $event = null;
	
	public function testAddHandler()
	{
		$this->eventDispatcher->addHandler('New Event1', $this->eventHandler);
		$attr = $this->readAttribute($this->eventDispatcher, 'handlers');
		$this->assertInstanceOf('unittest\module\unittestEventHandler', $attr['New Event1'][0]);
		$this->eventDispatcher->addHandler('New Event1', $this->eventHandler);
		$attr = $this->readAttribute($this->eventDispatcher, 'handlers');
		$this->assertInstanceOf('unittest\module\unittestEventHandler', $attr['New Event1'][1]);
		$this->eventDispatcher->addHandler('New Event2', $this->eventHandler);
		$attr = $this->readAttribute($this->eventDispatcher, 'handlers');
		$this->assertInstanceOf('unittest\module\unittestEventHandler', $attr['New Event2'][0]);
	}
	
	public function testTriggerEvent()
	{
		$event = $this->eventDispatcher->triggerEvent($this->event);	
		$this->assertInstanceOf ( 'jamwork\common\Event', $event);
		$event = $this->eventDispatcher->triggerEvent('New Event');
		$this->assertInstanceOf ( 'jamwork\common\Event', $event);
		$this->eventDispatcher->addHandler('new Event', $this->eventHandler);
		$this->eventDispatcher->addHandler('new Event', $this->eventHandler);
		$event = $this->eventDispatcher->triggerEvent($this->event);
		$attr = $this->readAttribute($this->eventDispatcher, 'iterator');
		$this->assertEquals('2', $attr);	
	}
	
	public function testTriggerEvent_CallEvents()
	{
		$mockEvent = $this->getMock('jamwork\common\Event', array(), array('new Event'));
		$mockEvent->expects($this->once())->method('getName');
		$this->eventDispatcher->addHandler('new Mock Event', $this->eventHandler);
		$this->eventDispatcher->triggerEvent($mockEvent);
	}
	
	public function testTriggerEvent_Break()
	{
		$this->eventHandlerCancel = new UnittestCancelEventHandler();
		$this->eventDispatcher->addHandler('new Event', $this->eventHandler);
		$this->eventDispatcher->addHandler('new Event', $this->eventHandlerCancel);
		$this->eventDispatcher->triggerEvent($this->event);
		$attr = $this->readAttribute($this->eventDispatcher, 'iterator');
		$this->assertEquals('1', $attr);
	}
		
	protected function setUp()
	{
		$this->eventDispatcher = new EventDispatcher();
		$this->eventHandler = new UnittestEventHandler();
		$this->event = new Event('new Event');
	}
	
	protected function tearDown()
	{
		unset($this->eventDispatcher);
		unset($this->eventHandler);
		unset($this->event);
	}
}