<?php

namespace unittest\jamwork\common;

use jamwork\common\Event;

class EventTest extends \PHPUnit_Framework_TestCase 
{
	protected $event = null;
	
	public function testGetName()
	{
		$this->assertSame('test Event', $this->event->getName());		
	}
	
	public function testGetContext()
	{
		$this->assertSame('Context', $this->event->getContext());		
	}
	
	public function testGetInfo()
	{
		$this->assertSame('info', $this->event->getInfo());		
	}
	
	public function testIsCanceled()
	{
		$this->assertFalse($this->event->isCanceled());
		$this->event->cancel();
		$this->assertTrue($this->event->isCanceled());
	}
	
	public function testCancel()
	{
		// wird indirekt in testIsCanceled() getestet...
	}
		
	protected function setUp()
	{
		$this->event = new Event('test Event', 'Context', 'info');
	}
	
	protected function tearDown()
	{
		unset($this->event);
	}
}