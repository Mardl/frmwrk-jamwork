<?php

namespace unittest\jamwork\table;

use \jamwork\table\Table;

class TableTest extends \PHPUnit_Framework_TestCase
{
	private $table = NULL;
	private $output = NULL;
			
	public function testId()
	{
		$this->output->expects($this->exactly(1))
		     ->method('setId');	
		
		$this->table->id(1);
	}
	
	public function testAddClass()
	{
		$this->output->expects($this->exactly(1))
		     ->method('addClass');	
		
		$this->table->addClass('foo');
	}
	
	public function testThead_empty()
	{
		$this->output->expects($this->exactly(1))
		     ->method('setHead');
			 
		$head = $this->table->thead();		
			
		$this->assertInstanceOf('jamwork\table\TableBody', $head);
	}
	
	public function testThead_filled()
	{
		$thead = $this->getMockBuilder('jamwork\table\TableBody')
                     	 	 ->disableOriginalConstructor()
                     	   	 ->getMock();
							 
		$refl = new \ReflectionObject($this->table);
		$prop = $refl->getProperty('thead');
		$prop->setAccessible(true);
		$prop->setValue($this->table, $thead);	
			
		$this->output->expects($this->exactly(0))
		     ->method('setHead');
			 
		$head = $this->table->thead();		
			
		$this->assertInstanceOf('jamwork\table\TableBody', $head);
	}
	
	public function testTfoot_empty()
	{
		$this->output->expects($this->exactly(1))
		     ->method('setFoot');
			 
		$foot = $this->table->tfoot();		
			
		$this->assertInstanceOf('jamwork\table\TableBody', $foot);
	}
	
	public function testTfoot_filled()
	{
		$tfoot = $this->getMockBuilder('jamwork\table\TableBody')
                     	 	 ->disableOriginalConstructor()
                     	   	 ->getMock();
							 
		$refl = new \ReflectionObject($this->table);
		$prop = $refl->getProperty('tfoot');
		$prop->setAccessible(true);
		$prop->setValue($this->table, $tfoot);	
			
		$this->output->expects($this->exactly(0))
		     ->method('setFoot');
			 
		$foot = $this->table->tfoot();		
			
		$this->assertInstanceOf('jamwork\table\TableBody', $foot);
	}
	
	public function testTbody_empty()
	{
		$this->output->expects($this->exactly(1))
		     ->method('setBody');
			 
		$body = $this->table->tbody();		
			
		$this->assertInstanceOf('jamwork\table\TableBody', $body);
	}
	
	public function testTbody_filled()
	{
		$tbody = $this->getMockBuilder('jamwork\table\TableBody')
                     	 	 ->disableOriginalConstructor()
                     	   	 ->getMock();
							 
		$refl = new \ReflectionObject($this->table);
		$prop = $refl->getProperty('tbody');
		$prop->setAccessible(true);
		$prop->setValue($this->table, $tbody);	
			
		$this->output->expects($this->exactly(0))
		     ->method('setBody');
			 
		$body = $this->table->tbody();		
			
		$this->assertInstanceOf('jamwork\table\TableBody', $body);
	}
	
	public function testCreate()
	{
		$this->output->expects($this->exactly(1))
		     ->method('generate');
		
		$this->table->create();
	}
	
	public function test__toString()
	{
		$this->output->expects($this->exactly(1))
		     ->method('generate')
			 ->will($this->returnValue('test for __toString'));
			 
		$str = $this->table->__toString();
		$this->assertSame('test for __toString', $str);
	}
	
	public function test__clone()
	{
		$table = clone $this->table;
		$attr = $this->readAttribute($this->table, 'output');	
		
		$this->assertInstanceOf('\jamwork\table\Table', $table);
	}
	
	public function testGetTableOutput()
	{
		$output = $this->table->getTableOutput();	
		$this->assertInstanceOf('jamwork\table\TableHTML', $output);
	}
	
	protected function setUp()
	{
		$this->output = $this->getMockBuilder('jamwork\table\TableHTML')
                     	 	 ->disableOriginalConstructor()
                     	   	 ->getMock();
							 
		$this->table = new table($this->output);
	}
	
	protected function tearDown()
	{
		unset($this->table);
		unset($this->output);
	} 

}
