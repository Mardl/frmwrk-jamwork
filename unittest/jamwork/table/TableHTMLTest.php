<?php

namespace unittest\jamwork\table;

use \jamwork\table\TableHTML;

class TableHTMLTest extends \PHPUnit_Framework_TestCase
{
	private $table = NULL;
	private $head = NULL;
	private $foot = NULL;
	private $body = NULL;	
		
	public function testGenerate()
	{
		$row = $this->getMockBuilder('jamwork\table\TableRow')
                     	 	 ->disableOriginalConstructor()
                     	   	 ->getMock();
							 
		$cells[] =  array('data' => 'foo', 'class' => 'foo', 'colspan' => 0);
		$cells[] =  array('data' => 'foo2', 'class' => 'foo2', 'colspan' => 0);					 
			
		$this->table->setHead( $this->head );
		$this->table->setBody( $this->body );
		$this->table->setFoot( $this->foot );
		
		$this->head->expects($this->exactly(2))
		     ->method('getRows')
			 ->will($this->returnValue(array($row, $row)));
			 
		$this->body->expects($this->exactly(2))
		     ->method('getRows')
			 ->will($this->returnValue(array($row, $row)));
			 
		$this->foot->expects($this->exactly(2))
		     ->method('getRows')
			 ->will($this->returnValue(array($row, $row)));
			 
		$row->expects($this->exactly(12))
		     ->method('getCells')
			 ->will($this->returnValue($cells));
			 
		$this->head->expects($this->exactly(1))
		     ->method('getClasses')
			 ->will($this->returnValue('head'));
			 
		$this->body->expects($this->exactly(1))
		     ->method('getClasses')
			 ->will($this->returnValue('body'));
			 
		$this->foot->expects($this->exactly(1))
		     ->method('getClasses')
			 ->will($this->returnValue('foot'));
			 
		$row->expects($this->exactly(6))
		     ->method('getClasses')
			 ->will($this->returnValue('row'));
		
		$table = $this->table->generate();

		$this->assertContains('<table>', $table);
		$this->assertContains('<td class="foo">foo</td>', $table);
		$this->assertContains('<tr class="row">', $table);
		$this->assertContains('<th class="foo">foo</th>', $table);
		$this->assertContains('<tbody class="body">', $table);
		$this->assertContains('<thead class="head">', $table);
		$this->assertContains('<tfoot class="foot">', $table);
	}
	
	public function testSetHead()
	{
		$attr= $this->readAttribute($this->table, 'head');
		
		$this->assertEmpty($attr);
			
		$this->table->setHead( $this->head );
		
		$attr= $this->readAttribute($this->table, 'head');
		
		$this->assertInstanceOf('jamwork\table\TableBody', $attr);	
	}
	
	public function testSetBody()
	{
		$attr= $this->readAttribute($this->table, 'body');
		
		$this->assertEmpty($attr);
			
		$this->table->setBody( $this->body );
		
		$attr= $this->readAttribute($this->table, 'body');
		
		$this->assertInstanceOf('jamwork\table\TableBody', $attr);
	}
	
	public function testSetFoot()
	{
		$attr= $this->readAttribute($this->table, 'foot');
		
		$this->assertEmpty($attr);
			
		$this->table->setFoot( $this->foot );
		
		$attr= $this->readAttribute($this->table, 'foot');
		
		$this->assertInstanceOf('jamwork\table\TableBody', $attr);
	}
	
	public function testSetId()
	{
		$attr= $this->readAttribute($this->table, 'id');
		
		$this->assertEmpty($attr);
			
		$this->table->setId( 1 );
		
		$attr= $this->readAttribute($this->table, 'id');
		
		$this->assertSame(1, $attr);
	}
	
	public function testGetId()
	{
		//siehe testInsertId
	}
	
	public function testAddClass()
	{
		$attr= $this->readAttribute($this->table, 'classes');
		
		$this->assertEmpty($attr);
			
		$this->table->addClass(1);
		
		$attr= $this->readAttribute($this->table, 'classes');
		
		$this->assertSame(1, count($attr));
		
		$this->table->addClass(1);
		
		$attr= $this->readAttribute($this->table, 'classes');
		
		$this->assertSame(2, count($attr));	
		
	}
	
	public function testCreate()
	{	
		//siehe testGenerate
	}
	
	public function testCountMaxCells()
	{
		//siehe testGenerate
	}
	
	public function testMaxCellsInRow()
	{
		$row1 = $this->getMockBuilder('jamwork\table\TableRow')
                     	 	 ->disableOriginalConstructor()
                     	   	 ->getMock();
							 
		$row2 = $this->getMockBuilder('jamwork\table\TableRow')
                     	 	 ->disableOriginalConstructor()
                     	   	 ->getMock();
							 
		$this->head->expects($this->exactly(1))
		     ->method('getRows')
			 ->will($this->returnValue(array($row1, $row2)));
		
		$row1->expects($this->exactly(3))
		     ->method('getCells')
			 ->will($this->returnValue(array(1,2)));
			 
		$row2->expects($this->exactly(1))
		     ->method('getCells')
			 ->will($this->returnValue(array(1,2,3)));
			 
		$this->foot->expects($this->exactly(1))
		     ->method('getRows')
			 ->will($this->returnValue(array($row1, $row1)));
		
			
		$method = new \ReflectionMethod(
          $this->table, 'maxCellsInRow'
        );
	    $method->setAccessible(TRUE);
		$method->invokeArgs($this->table, array($this->head));
		$method->invokeArgs($this->table, array($this->foot));
		
		$cells = $this->readAttribute($this->table, 'maxCells');
		
		$this->assertSame(3, $cells);	
	}

	public function testInsertId()
	{
		$this->table->setId( 1 );	
			
		$method = new \ReflectionMethod(
          $this->table, 'insertId'
        );
	    $method->setAccessible(TRUE);
		$id = $method->invoke($this->table);
		$this->assertSame(' id="1"', $id);
	}
	
	public function testInsertClasses()
	{
		//siehe testGenerate
	}
	
	public function testCreateHead()
	{
		//siehe testGenerate
	}
	
	public function testCreateBody()
	{
		//siehe testGenerate
	}
	
	public function testCreateFoot()
	{
		//siehe testGenerate
	}
	
	public function testCreateRows()
	{
		//siehe testGenerate
	}
	
	public function testCreateCells()
	{
		//siehe testGenerate
	}
	
	public function testInsertAttr()
	{
		$cell['foo'] = 1;
		$attr = 'foo';
			
		$method = new \ReflectionMethod(
          $this->table, 'insertAttr'
        );
	    $method->setAccessible(TRUE);
		$attribute = $method->invokeArgs($this->table, array($cell, $attr));	
		
		$this->assertSame(' foo="1"', $attribute);
		
		$cell['foo'] = '';
		
		$attribute = $method->invokeArgs($this->table, array($cell, $attr));	
		
		$this->assertEmpty($attribute);
	}
	
	public function testGetClasses()
	{
		$method = new \ReflectionMethod(
          $this->table, 'getClasses'
        );
	    $method->setAccessible(TRUE);
		$class = $method->invoke($this->table);
		$this->assertEmpty($class);	
			
		$this->table->addClass('foo');
		$this->table->addClass('oof');	
			
		$class = $method->invoke($this->table);
		
		$this->assertSame('foo oof', $class);
	}
	
	public function testNl()
	{
		$method = new \ReflectionMethod(
          $this->table, 'nl'
        );
	    $method->setAccessible(TRUE);
		$nl = $method->invoke($this->table);
		$this->assertSame("\n", $nl);	
	}
	
	public function testTab()
	{
		$method = new \ReflectionMethod(
          $this->table, 'tab'
        );
	    $method->setAccessible(TRUE);
		
		$nl = $method->invoke($this->table, 1);
		$this->assertSame("\t", $nl);
		
		$nl = $method->invoke($this->table, 5);
		$this->assertSame("\t\t\t\t\t", $nl);		
	}
	
	public function test__clone()
	{
		$clone = clone $this->table;
		$this->assertInstanceOf('\jamwork\table\TableHTML',$this->table);
	}
		
	protected function setUp()
	{
		$this->head	= $this->getMockBuilder('jamwork\table\TableBody')
                     	 	 ->disableOriginalConstructor()
                     	   	 ->getMock();
							 
		$this->foot	= $this->getMockBuilder('jamwork\table\TableBody')
                     	 	 ->disableOriginalConstructor()
                     	   	 ->getMock();
							 
		$this->body	= $this->getMockBuilder('jamwork\table\TableBody')
                     	 	 ->disableOriginalConstructor()
                     	   	 ->getMock();
		
		$this->table = new TableHTML();
	}
	
	
	protected function tearDown()
	{
		unset($this->table);
		unset($this->head);
		unset($this->body);
		unset($this->foot);
	} 
}
