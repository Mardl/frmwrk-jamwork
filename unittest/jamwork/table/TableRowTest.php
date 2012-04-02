<?php

namespace unittest\jamwork\table;

use \jamwork\table\TableRow;

class TableRowTest extends \PHPUnit_Framework_TestCase
{
	public function testAddCell()
	{
		$data = 'data'; 
		$cls = 'cls'; 
		$colspan = 4;	
			
		$attr = $this->readAttribute($this->table, 'cells');
		$this->assertEmpty($attr);
		
		$this->table->addCell($data, $cls, $colspan);
			
		$attr = $this->readAttribute($this->table, 'cells');
		$this->assertSame(array('data' => $data, 'class' => $cls, 'colspan' => $colspan), $attr[0]);
		
		$data = ''; 
		$cls = ''; 
		$colspan = 0;
		
		$this->table->addCell($data, $cls, $colspan);
			
		$attr = $this->readAttribute($this->table, 'cells');
		$this->assertSame(array('data' => $data, 'class' => $cls, 'colspan' => $colspan), $attr[1]);
	}
	
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
	
	public function testGetClasses()
	{
		$class = $this->table->getClasses();
		$this->assertEmpty($class);	
			
		$this->table->addClass('foo');
		$this->table->addClass('oof');	
			
		$class = $this->table->getClasses();
		
		$this->assertSame('foo oof', $class);
	}
	
	public function testGetCells()
	{
		$data = 'data'; 
		$cls = 'cls'; 
		$colspan = 4;	
			
		$attr = $this->readAttribute($this->table, 'cells');
		$this->assertEmpty($attr);
		
		$this->table->addCell($data, $cls, $colspan);
			
		$attr = $this->table->getCells();
		$this->assertSame(array('data' => $data, 'class' => $cls, 'colspan' => $colspan), $attr[0]);
		
		$data = ''; 
		$cls = ''; 
		$colspan = 0;
		
		$this->table->addCell($data, $cls, $colspan);
			
		$attr = $this->table->getCells();
		$this->assertSame(array('data' => $data, 'class' => $cls, 'colspan' => $colspan), $attr[1]);
	}
	
	public function testGetCell()
	{
		$i = 0;	
		$data = 'data'; 
		$cls = 'cls'; 
		$colspan = 4;
			
		$this->assertFalse($this->table->getCell($i));
		
		$this->table->addCell($data, $cls, $colspan);
		
		$cell = $this->table->getCell($i);
		$this->assertSame(array('data' => $data, 'class' => $cls, 'colspan' => $colspan), $cell);
	}
	
	public function testIssetCell()
	{
		$i = 0;
		
		$this->assertFalse($this->table->issetCell($i));
		$this->table->addCell(1, 2, 3);
		
		$this->assertTrue($this->table->issetCell($i));
	}
	
	public function testGetLastCell()
	{
		$i = 0;	
		$data = 'data'; 
		$cls = 'cls'; 
		$colspan = 4;
			
		$this->assertFalse($this->table->getLastCell());
		$this->table->addCell(1, 2, 3);	
		$this->table->addCell($data, $cls, $colspan);
		
		$this->assertSame(array('data' => $data, 'class' => $cls, 'colspan' => $colspan), $this->table->getLastCell());
	}
	
	public function testGetFirstCell()
	{
		$i = 0;	
		$data = 'data'; 
		$cls = 'cls'; 
		$colspan = 4;
			
		$this->assertFalse($this->table->getFirstCell());
		$this->table->addCell($data, $cls, $colspan);
		$this->table->addCell(1, 2, 3);		
		
		$this->assertSame(array('data' => $data, 'class' => $cls, 'colspan' => $colspan), $this->table->getFirstCell());
	}
	
	protected function setUp()
	{
		
		$this->table = new TableRow();
	}
	
	protected function tearDown()
	{
		unset($this->table);
	} 
}
