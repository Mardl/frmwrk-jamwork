<?php

namespace unittest\jamwork\modulverwaltung;

use \jamwork\modulverwaltung\ModulController;

class ModulControllerTest extends \PHPUnit_Framework_TestCase
{
	private $controller = null;
	
	public function testAddPrev()
	{
		$runPrev = $this->readAttribute($this->controller, 'runPrev');
		$this->assertEmpty($runPrev);
		
		$this->controller->addPrev('\some\className');
		
		$runPrev = $this->readAttribute($this->controller, 'runPrev');
		$this->assertSame($runPrev, array('\some\className'));
	}
	
	public function testReadModule()
	{
		$method = new \ReflectionMethod($this->controller, 'readModule');
		$method->setAccessible(true);
		
		$modulMap = $this->readAttribute($this->controller, 'modulMap');
		$this->assertEmpty($modulMap);
		
		$method->invoke($this->controller);
		
		$modulMap = $this->readAttribute($this->controller, 'modulMap');
		$this->assertTrue(count($modulMap) == 3);
		foreach($modulMap as $modulObject)
		{
			$this->assertInstanceOf('\jamwork\modulverwaltung\ModulObject', $modulObject);
		}
	}
	
	public function testGetModulObjects()
	{
		$modulObjects = $this->controller->getModulObjects();
		$this->assertSame($modulObjects, $this->readAttribute($this->controller, 'modulMap'));
		
		// Beim wiederholten Aufruf sollte readModule nicht nochmal ausgeführt werden...
		$modulObjects2 = $this->controller->getModulObjects();
		$this->assertSame($modulObjects2, $modulObjects);
	}
	
	public function testGetModulObjects_ksort()
	{
		$controller1 = new ModulController('unittest/module/', dirname(__FILE__).'/../../../');
		$controller2 = new ModulController('unittest/module/', dirname(__FILE__).'/../../../');
		
		$controller1->addPrev('unittest\module\testmodulnoname');
		
		$modulObjects1 = $controller1->getModulObjects();
		$modulObjects2 = $controller2->getModulObjects();
		
		// KeySort
		$controller1_modulMap = $this->readAttribute($controller1, 'modulMap');
		ksort($controller1_modulMap);
		$this->assertSame($modulObjects1, $controller1_modulMap);
			
		// Keys der Objekte
		$this->assertTrue(isset($modulObjects1[0]) && isset($modulObjects1[1000]) && isset($modulObjects1[1001]));
		$this->assertTrue(isset($modulObjects2[1000]) && isset($modulObjects2[1001]) && isset($modulObjects2[1002]));
		
		// Erstes Object wird definiert mit => $controller1->addPrev('unittest\module\testmodulnoname');
		$ns1 = $this->readAttribute($modulObjects1[0], 'namespace');
		$this->assertSame($ns1, 'unittest\module\testmodulnoname');
	}
	
	public function setUp()
	{
		$this->controller = new ModulController('unittest/module/', dirname(__FILE__).'/../../../');
	}
	
	public function tearDown()
	{
		unset($this->controller);
	}
}
