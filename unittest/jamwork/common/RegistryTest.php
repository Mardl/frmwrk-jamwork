<?php

namespace unittest\jamwork\common;

use \jamwork\common\Registry;
use \jamwork\common\HttpResponse;
use \jamwork\common\HttpRequest;
use \jamwork\common\EventDispatcher;
use \jamwork\database\Database;
use \jamwork\template\HtmlTemplate;

class RegistryTest extends \PHPUnit_Framework_TestCase 
{
	private $registry;
	private $request;
	private $response;
	private $mockDatabase;
	private $template;
	
	public function test__clone()
	{
		$method = new \ReflectionMethod($this->registry, '__clone');
		$this->assertTrue( $method->isPrivate() );
	}
	
	public function test__construct()
	{
		$method = new \ReflectionMethod($this->registry, '__construct');
		$this->assertTrue( $method->isProtected() );
	}
	
	public function testGetInstance()
	{
		$instance = $this->registry->getInstance();
		$this->assertSame($instance, $this->registry);
		
		$instance = Registry::getInstance();
		$this->assertSame($instance, $this->registry);
	}
	
	public function test__set()
	{
		$this->registry->foo = 'bar';
		$values = $this->readAttribute($this->registry, 'values');
		$this->assertSame($values['registry']['foo'], 'bar');
	}
	
	public function test__get()
	{
		$this->registry->foo = 'bar';
		$this->assertSame($this->registry->foo, 'bar');
	}
	
	public function test__unset()
	{
		$this->registry->foo = 'bar';
		$values = $this->readAttribute($this->registry, 'values');
		$this->assertTrue( isset($values['registry']['foo']) );
		
		unset($this->registry->foo);
		$values = $this->readAttribute($this->registry, 'values');
		$this->assertFalse( isset($values['registry']['foo']) );
	}
	
	public function test__isset()
	{
		$this->registry->foo = 'bar';
		$this->assertTrue( isset($this->registry->foo) );
		$this->assertFalse( isset($this->registry->bar) );
	}
		
	public function testGetRequest_Exception()
	{
		try
		{
			$request = $this->registry->getRequest();
		}
		catch( \Exception $e)
		{
			return;
		}
		
		$this->Fail('Exception erwartet!');
	}
	
	public function testGetResponse_Exception()
	{
		try
		{
			$request = $this->registry->getResponse();
		}
		catch( \Exception $e)
		{
			return;
		}
		
		$this->Fail('Exception erwartet!');
	}
	

	public function testSetRequest()
	{
		$values = $this->readAttribute($this->registry, 'values');
		$this->assertTrue(!isset($values['system']['request']));
		
		$this->registry->setRequest($this->request);
		
		$values = $this->readAttribute($this->registry, 'values');
		$this->assertSame($values['system']['request'], $this->request);
	}

	public function testGetRequest()
	{
		$this->registry->setRequest( $this->request );
		
		$request = $this->registry->getRequest();
		$this->assertSame($this->request, $request);
	}
	
	public function testHasRequest()
	{
		$this->assertFalse( $this->registry->hasRequest() );
		$this->registry->setRequest( $this->request );
		$this->assertTrue( $this->registry->hasRequest() );
	}

	public function testSetResponse()
	{
		$values = $this->readAttribute($this->registry, 'values');
		$this->assertTrue(!isset($values['system']['response']));
		
		$this->registry->setResponse($this->response);
		
		$values = $this->readAttribute($this->registry, 'values');
		$this->assertSame($values['system']['response'], $this->response);
	}

	public function testGetResponse()
	{
		$this->registry->setResponse($this->response);
		
		$response = $this->registry->getResponse();
		$this->assertSame($this->response, $response);
	}
	
	public function testHasResponse()
	{
		$this->assertFalse( $this->registry->hasResponse() );
		$this->registry->setResponse( $this->response );
		$this->assertTrue( $this->registry->hasResponse() );
	}

	public function testSetDatabase()
	{
		$values = $this->readAttribute($this->registry, 'values');
		$this->assertTrue(!isset($values['system']['database']));
		
		$this->registry->setDatabase($this->mockDatabase);
		
		$values = $this->readAttribute($this->registry, 'values');
		$this->assertSame($values['system']['database'], $this->mockDatabase);
	}

	public function testGetDatabase()
	{
		$this->registry->setDatabase($this->mockDatabase);
		$database = $this->registry->getDatabase();
		$this->assertSame($this->mockDatabase, $database);
	}
		
	public function testHasDatabase()
	{
		$this->assertFalse( $this->registry->hasDatabase() );
		$this->registry->setDatabase( $this->mockDatabase );
		$this->assertTrue( $this->registry->hasDatabase() );
	}

	public function testSetTemplate()
	{
		$values = $this->readAttribute($this->registry, 'values');
		$this->assertTrue(!isset($values['system']['template']));
		
		$this->registry->setTemplate($this->template);
		
		$values = $this->readAttribute($this->registry, 'values');
		$this->assertSame($values['system']['template'], $this->template);
	}

	public function testGetTemplate()
	{
		$this->registry->setTemplate($this->template);
		$template = $this->registry->getTemplate();
		$this->assertSame($this->template, $template);
	}
		
	public function testHasTemplate()
	{
		$this->assertFalse( $this->registry->hasTemplate() );
		$this->registry->setTemplate( $this->template );
		$this->assertTrue( $this->registry->hasTemplate() );
	}

	public function testGet_Exception()
	{
		try
		{
			$request = $this->registry->UnitKey1;
		}
		catch( \Exception $e)
		{
			return;
		}
		
		$this->Fail('Exception erwartet!');
	}
			
	public function testSet()
	{
		$this->assertFalse(isset($this->registry->Key1));

		$this->registry->Key1 = 'Value1';
		
		$this->assertTrue(isset($this->registry->Key1));
		
	}
	
	public function testGet()
	{
		$this->registry->Key2 = 'Value1';
		
		$value = $this->registry->Key2;
		
		$this->assertSame('Value1', $value);
	}

	public function testUnset()
	{

		$this->registry->Key1 = 'Value1';
		
		$this->assertTrue(isset($this->registry->Key1));

		unset($this->registry->Key1);
		
		$this->assertFalse(isset($this->registry->Key1));

	}

	public function testGet_mitRequest()
	{
		$this->registry->setRequest($this->request);
		$this->registry->request = 'Value1';
		
		$request = $this->registry->getRequest();
		$value = $this->registry->request;

		$this->assertSame($this->request, $request);		
		$this->assertSame('Value1', $value);
	}
			
	public function testSetEventDispatcher()
	{
		$values = $this->readAttribute($this->registry, 'values');
		$this->assertTrue(!isset($values['system']['eventDispatcher']));
		
		$this->registry->setEventDispatcher($this->eventDispatcher);
		
		$values = $this->readAttribute($this->registry, 'values');
		$this->assertSame($values['system']['eventDispatcher'], $this->eventDispatcher);
	}

	public function testGetEventDispatcher()
	{
		$this->registry->setEventDispatcher($this->eventDispatcher);
		$eventDispatcher = $this->registry->getEventDispatcher();
		$this->assertSame($this->eventDispatcher, $eventDispatcher);
	}
	
	public function testHasEventDispatcher()
	{
		$this->assertFalse( $this->registry->hasEventDispatcher() );
		$this->registry->setEventDispatcher( $this->eventDispatcher );
		$this->assertTrue( $this->registry->hasEventDispatcher() );
	}
	
	public function testReset()
	{
		$registry = Registry::getInstance();
		$this->assertSame($registry, $this->registry);
		
		$registry->reset();
		$registry = Registry::getInstance();
		$this->assertNotSame($registry, $this->registry);
	}
	
	public function testHasKey_empty()
	{
		$this->registry->foo = 'bar';
		
		$method = new \ReflectionMethod($this->registry, 'hasKey');
		$method->setAccessible(true);
		$ret = $method->invokeArgs($this->registry, array('', ''));
		
		$this->assertFalse($ret);
	}
	
	public function testHasKey_negative()
	{
		$this->registry->foo = 'bar';
		
		$method = new \ReflectionMethod($this->registry, 'hasKey');
		$method->setAccessible(true);
		$ret = $method->invokeArgs($this->registry, array('foo', 'bar'));
		
		$this->assertFalse($ret);
	}
	
	public function testHasKey_positiv()
	{
		$this->registry->foo = 'bar';
		
		$method = new \ReflectionMethod($this->registry, 'hasKey');
		$method->setAccessible(true);
		$ret = $method->invokeArgs($this->registry, array('foo', 'registry'));
		
		$this->assertTrue($ret);
	}
	
	public function testUnsetKey()
	{
		$this->registry->foo = 'bar';
		
		$values = $this->readAttribute($this->registry, 'values');
		$this->assertSame($values['registry']['foo'], 'bar');
		
		$method = new \ReflectionMethod($this->registry, 'unsetKey');
		$method->setAccessible(true);
		$method->invokeArgs($this->registry, array('foo', 'registry'));
		
		$values = $this->readAttribute($this->registry, 'values');
		$this->assertTrue(!isset($values['registry']['foo']));
	}
	
	public function testSetSession()
	{
		$values = $this->readAttribute($this->registry, 'values');
		$this->assertTrue(!isset($values['system']['session']));
		
		$this->registry->setSession($this->mockSession);
		
		$values = $this->readAttribute($this->registry, 'values');
		$this->assertSame($values['system']['session'], $this->mockSession);
	}
	
	public function testGetSession()
	{
		$this->registry->setSession($this->mockSession);
		$session= $this->registry->getSession();
		$this->assertSame($this->mockSession, $session);
	}
	
	public function testHasSession()
	{
		$this->assertFalse( $this->registry->hasSession() );
		$this->registry->setSession( $this->mockSession );
		$this->assertTrue( $this->registry->hasSession() );
	}
	
	protected function setUp()
	{
		$this->request = new HttpRequest(array(),array(),array(), array());
		$this->response = new HttpResponse();
		$this->template = new HtmlTemplate('module/');
		$this->mockDatabase = $this->getMock('jamwork\database\Database',array(),array(),'',false);
		$this->mockSession = $this->getMock('jamwork\common\Session');
		$this->eventDispatcher = new eventDispatcher();
		$this->registry = Registry::getInstance();
	}
	
	protected function tearDown()
	{
		Registry::reset();
		unset($this->registry);
		unset($this->response);
		unset($this->template);
		unset($this->mockDatabase);
		unset($this->eventDispatcher);
		unset($this->registry );
		
	}
}
