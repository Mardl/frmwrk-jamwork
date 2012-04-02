<?php

namespace unittest\jamwork\debug;

use \jamwork\debug\DebugLogger;

class DebugLoggerTest extends \PHPUnit_Framework_TestCase
{
	public function testGetInstance()
    {
       $this->assertInstanceOf('jamwork\debug\DebugLogger', DebugLogger::getInstance());
    }
	
	public function test__clone()
    {
    	//nur da um clonen zu verhindern
    }
	
	public function testSetActive()
	{
		$logger = DebugLogger::getInstance();	
		$logger->setActive(true);
		$attr = $this->readAttribute($logger, 'active');	
		$this->assertTrue($attr);
		$logger->setActive(false);
		$attr = $this->readAttribute($logger, 'active');	
		$this->assertFalse($attr);
	}
	
	public function testSetShowTodo()
	{
		$logger = DebugLogger::getInstance();	
		$attr = $this->readAttribute($logger, 'showTodo');	
		$this->assertFalse($attr);
		$logger->setShowTodo(true);
		$attr = $this->readAttribute($logger, 'showTodo');	
		$this->assertTrue($attr);
		$logger->setShowTodo(false);
		$attr = $this->readAttribute($logger, 'showTodo');	
		$this->assertFalse($attr);
	}
	
	public function testIsActive()
	{
		$logger = DebugLogger::getInstance();	
		$method = new \ReflectionMethod($logger, 'isActive');
		$method->setAccessible(true);
		$attr = $method->invoke($logger);
		$this->assertFalse($attr);
		$logger->setActive(true);
		$method = new \ReflectionMethod($logger, 'isActive');
		$method->setAccessible(true);
		$attr = $method->invoke($logger);$this->assertTrue($attr);
		$logger->setActive(false);
		$method = new \ReflectionMethod($logger, 'isActive');
		$method->setAccessible(true);
		$attr = $method->invoke($logger);$this->assertFalse($attr);
	}
	
	public function testIsShowTodo()
	{
		$logger = DebugLogger::getInstance();	
		$method = new \ReflectionMethod($logger, 'isShowTodo');
		$method->setAccessible(true);
		$attr = $method->invoke($logger);	
		$this->assertFalse($attr);
		$logger->setShowTodo(true);
		$method = new \ReflectionMethod($logger, 'isShowTodo');
		$method->setAccessible(true);
		$attr = $method->invoke($logger);	$this->assertTrue($attr);
		$logger->setShowTodo(false);
		$method = new \ReflectionMethod($logger, 'isShowTodo');
		$method->setAccessible(true);
		$attr = $method->invoke($logger);	$this->assertFalse($attr);
	}
	
	public function testRam()
	{
		// macht keinen Sinn zu testen!
	}
	

	public function testLog_active()
	{
		$fb = $this->getMockBuilder('\FirePHP')
                   ->disableOriginalConstructor()
                   ->getMock();
				   
		$logger = DebugLogger::getInstance();	
			
		$refl = new \ReflectionObject( $logger);
		$prop = $refl->getProperty('fb');
		$prop->setAccessible(true);
		$prop->setValue($logger, $fb);
		
		$logger->setActive(true);
		
		$fb->expects($this->exactly(1))
             ->method('log')
             ->will($this->returnValue(true));
		
		$logger->log($logger, "Unittest");
	}
	

	public function testLog_notactive()
	{
		$fb = $this->getMockBuilder('\FirePHP')
                   ->disableOriginalConstructor()
                   ->getMock();
				   
		$logger = DebugLogger::getInstance();	
			
		$refl = new \ReflectionObject( $logger);
		$prop = $refl->getProperty('fb');
		$prop->setAccessible(true);
		$prop->setValue($logger, $fb);
		
		$logger->setActive(false);
		
		$fb->expects($this->exactly(0))
             ->method('log')
             ->will($this->returnValue(true));
		
		$logger->log($logger, "Unittest");
	}
	
	public function testTodo_isShowTodoTrue()
	{
		$fb = $this->getMockBuilder('\FirePHP')
                   ->disableOriginalConstructor()
                   ->getMock();
				   
		$logger = DebugLogger::getInstance();	
			
		$refl = new \ReflectionObject( $logger);
		$prop = $refl->getProperty('fb');
		$prop->setAccessible(true);
		$prop->setValue($logger, $fb);
		
		$logger->setShowTodo(true);
		
		$fb->expects($this->exactly(1))
             ->method('info')
             ->will($this->returnValue(true));
		
		$logger->Todo($logger, "Unittest","");
	}
	
	public function testLog_isShowTodofalse()
	{
		$fb = $this->getMockBuilder('\FirePHP')
                   ->disableOriginalConstructor()
                   ->getMock();
				   
		$logger = DebugLogger::getInstance();	
			
		$refl = new \ReflectionObject( $logger);
		$prop = $refl->getProperty('fb');
		$prop->setAccessible(true);
		$prop->setValue($logger, $fb);
		
		$logger->setShowTodo(false);
		
		$fb->expects($this->exactly(0))
             ->method('log')
             ->will($this->returnValue(true));
		
		$logger->todo($logger, "Unittest","");
	}
	
	public function testInfo_active()
	{
		$fb = $this->getMockBuilder('\FirePHP')
                   ->disableOriginalConstructor()
                   ->getMock();
				   
		$logger = DebugLogger::getInstance();	
			
		$refl = new \ReflectionObject( $logger);
		$prop = $refl->getProperty('fb');
		$prop->setAccessible(true);
		$prop->setValue($logger, $fb);
		
		$logger->setActive(true);
		$logger->setShowInfo(true);
		
		$fb->expects($this->exactly(1))
             ->method('info')
             ->will($this->returnValue(true));
		
		$logger->info($logger, "Unittest");
	}
	
	public function testInfo_notactive()
	{
		$fb = $this->getMockBuilder('\FirePHP')
                   ->disableOriginalConstructor()
                   ->getMock();
				   
		$logger = DebugLogger::getInstance();	
			
		$refl = new \ReflectionObject( $logger);
		$prop = $refl->getProperty('fb');
		$prop->setAccessible(true);
		$prop->setValue($logger, $fb);
		
		$logger->setActive(FALSE);
		$logger->setShowInfo(false);
		
		$fb->expects($this->exactly(0))
             ->method('info')
             ->will($this->returnValue(true));
		
		$logger->info($logger, "Unittest");
	}
	
	public function testWarn_active()
	{
		$fb = $this->getMockBuilder('\FirePHP')
                   ->disableOriginalConstructor()
                   ->getMock();
				   
		$logger = DebugLogger::getInstance();	
			
		$refl = new \ReflectionObject( $logger);
		$prop = $refl->getProperty('fb');
		$prop->setAccessible(true);
		$prop->setValue($logger, $fb);
		
		$logger->setActive(true);
		
		$fb->expects($this->exactly(1))
             ->method('warn')
             ->will($this->returnValue(true));
		
		$logger->warn($logger, "Unittest");
	}
	
	public function testWarn_notactive()
	{
		$fb = $this->getMockBuilder('\FirePHP')
                   ->disableOriginalConstructor()
                   ->getMock();
				   
		$logger = DebugLogger::getInstance();	
			
		$refl = new \ReflectionObject( $logger);
		$prop = $refl->getProperty('fb');
		$prop->setAccessible(true);
		$prop->setValue($logger, $fb);
		
		$logger->setActive(false);
		
		$fb->expects($this->exactly(0))
             ->method('warn')
             ->will($this->returnValue(true));
		
		$logger->warn($logger, "Unittest");
	}
	
	public function testError_active()
	{
		$fb = $this->getMockBuilder('\FirePHP')
                   ->disableOriginalConstructor()
                   ->getMock();
				   
		$logger = DebugLogger::getInstance();	
			
		$refl = new \ReflectionObject( $logger);
		$prop = $refl->getProperty('fb');
		$prop->setAccessible(true);
		$prop->setValue($logger, $fb);
		
		$logger->setActive(true);
		
		$fb->expects($this->exactly(1))
             ->method('error')
             ->will($this->returnValue(true));
		
		$logger->error($logger, "Unittest");
	}
	
	public function testError_notactive()
	{
		$fb = $this->getMockBuilder('\FirePHP')
                   ->disableOriginalConstructor()
                   ->getMock();
				   
		$logger = DebugLogger::getInstance();	
			
		$refl = new \ReflectionObject( $logger);
		$prop = $refl->getProperty('fb');
		$prop->setAccessible(true);
		$prop->setValue($logger, $fb);
		
		$logger->setActive(false);
		
		$fb->expects($this->exactly(0))
             ->method('error')
             ->will($this->returnValue(true));
		
		$logger->error($logger, "Unittest");
	}
	
	public function testDump_active()
	{
		$fb = $this->getMockBuilder('\FirePHP')
                   ->disableOriginalConstructor()
                   ->getMock();
				   
		$logger = DebugLogger::getInstance();	
			
		$refl = new \ReflectionObject( $logger);
		$prop = $refl->getProperty('fb');
		$prop->setAccessible(true);
		$prop->setValue($logger, $fb);
		
		$logger->setActive(true);
		
		$fb->expects($this->exactly(1))
             ->method('dump')
             ->will($this->returnValue(true));
		
		$logger->dump($logger, "Unittest");
	}
	
	public function testDump_notactive()
	{
		$fb = $this->getMockBuilder('\FirePHP')
                   ->disableOriginalConstructor()
                   ->getMock();
				   
		$logger = DebugLogger::getInstance();	
			
		$refl = new \ReflectionObject( $logger);
		$prop = $refl->getProperty('fb');
		$prop->setAccessible(true);
		$prop->setValue($logger, $fb);
		
		$logger->setActive(false);
		
		$fb->expects($this->exactly(0))
             ->method('dump')
             ->will($this->returnValue(true));
		
		$logger->dump($logger, "Unittest");
	}
	
	public function testTrace_active()
	{
		$fb = $this->getMockBuilder('\FirePHP')
                   ->disableOriginalConstructor()
                   ->getMock();
				   
		$logger = DebugLogger::getInstance();	
			
		$refl = new \ReflectionObject( $logger);
		$prop = $refl->getProperty('fb');
		$prop->setAccessible(true);
		$prop->setValue($logger, $fb);
		
		$logger->setActive(true);
		
		$fb->expects($this->exactly(1))
             ->method('trace')
             ->will($this->returnValue(true));
		
		$logger->trace($logger, "Unittest");
	}
	
	public function testTrace_notactive()
	{
		$fb = $this->getMockBuilder('\FirePHP')
                   ->disableOriginalConstructor()
                   ->getMock();
				   
		$logger = DebugLogger::getInstance();	
			
		$refl = new \ReflectionObject( $logger);
		$prop = $refl->getProperty('fb');
		$prop->setAccessible(true);
		$prop->setValue($logger, $fb);
		
		$logger->setActive(false);
		
		$fb->expects($this->exactly(0))
             ->method('trace')
             ->will($this->returnValue(true));
		
		$logger->trace($logger, "Unittest");
	}
	
	public function testTable_active()
	{
		$fb = $this->getMockBuilder('\FirePHP')
                   ->disableOriginalConstructor()
                   ->getMock();
				   
		$logger = DebugLogger::getInstance();	
			
		$refl = new \ReflectionObject( $logger);
		$prop = $refl->getProperty('fb');
		$prop->setAccessible(true);
		$prop->setValue($logger, $fb);
		
		$logger->setActive(true);
		
		$fb->expects($this->exactly(1))
             ->method('table')
             ->will($this->returnValue(true));
		
		$logger->table($logger, "Unittest");
	}
	
	public function testTable_notactive()
	{
		$fb = $this->getMockBuilder('\FirePHP')
                   ->disableOriginalConstructor()
                   ->getMock();
				   
		$logger = DebugLogger::getInstance();	
			
		$refl = new \ReflectionObject( $logger);
		$prop = $refl->getProperty('fb');
		$prop->setAccessible(true);
		$prop->setValue($logger, $fb);
		
		$logger->setActive(false);
		
		$fb->expects($this->exactly(0))
             ->method('table')
             ->will($this->returnValue(true));
		
		$logger->table($logger, "Unittest");
	}
	
	public function testSetShowInfo()
	{
		$logger = DebugLogger::getInstance();
		$this->assertFalse($this->readAttribute($logger, 'showInfo'));
		$logger->setShowInfo(true);
		$this->assertTrue($this->readAttribute($logger, 'showInfo'));	
	}
	
	public function testIsShowInfo()
	{
		$logger = DebugLogger::getInstance();
		
		$method = new \ReflectionMethod(
          $logger, 'IsShowInfo'
        );
 		
	    $method->setAccessible(TRUE);
		$logger->setShowInfo(true);
		$this->assertTrue($method->invoke($logger));
		
			
	}
}
