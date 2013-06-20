<?php

namespace unittest\jamwork\table;

use \jamwork\table\TableBody;

class TableBodyTest extends \PHPUnit_Framework_TestCase
{
	public function testAddClass()
	{
		$attr = $this->readAttribute($this->table, 'classes');
		$this->assertEmpty($attr);
		
		$this->table->addClass('foo');
		
		$attr = $this->readAttribute($this->table, 'classes');
		$this->assertSame(array('foo'), $attr);
		
		$this->table->addClass('oof');
		
		$attr = $this->readAttribute($this->table, 'classes');
		$this->assertSame(array('foo', 'oof'), $attr);	

	}
	
	public function testRow()
	{
		$attr = $this->readAttribute($this->table, 'rows');
		$this->assertEmpty($attr);
			
		$row = $this->table->row();
		
		$attr = $this->readAttribute($this->table, 'rows');
		$this->assertInstanceOf('jamwork\table\TableRow', $attr[0]);
		$this->assertSame($row, $attr[0]);	
	}
	
	public function testGetLastRow()
	{
		$attr = $this->readAttribute($this->table, 'rows');
		$this->assertEmpty($attr);
		$lr = $this->table->getLastRow();
		$this->assertFalse($lr);
			
		$row = $this->table->row();
		$lr = $this->table->getLastRow();
		
		$this->assertInstanceOf('jamwork\table\TableRow', $lr);
		$this->assertSame($row, $lr);
	}
	
	public function testGetRow()
	{
		//siehe testGetLastRow()
	}
	
	
	public function testGetClasses()
	{
		$class = $this->table->getClasses();
		$this->assertEmpty($class);	
			
		$this->table->addClass('foo');
		$this->table->addClass('oof');	
			
		$class = $this->table->getClasses();
		
		$this->assertSame('foo oof', $class);
	}
	
	public function testGetRows()
	{
		$row = $this->table->row();
		$rows = $this->table->getRows();
		
		$this->assertSame($row, $rows[0]);
	}
	
	public function testIssetRow()
	{
		$i = 0;
		$this->assertFalse($this->table->issetRow($i));
		
		$this->table->row();
		$this->assertTrue($this->table->issetRow($i));	
	}
	
	public function testGetFirstRow()
	{
		$this->assertFalse($this->table->getFirstRow());
		$this->table->row();
		
		$attr = $this->readAttribute($this->table, 'rows');
		$this->assertSame($attr[0], $this->table->getFirstRow());
	}
	
	protected function setUp()
	{
		
		$this->table = new tableBody();
	}
	
	protected function tearDown()
	{
		unset($this->table);
	} 
}
