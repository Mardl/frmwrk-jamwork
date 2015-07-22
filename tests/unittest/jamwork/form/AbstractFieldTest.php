<?php

namespace unittest\jamwork\form;

use \jamwork\form\AbstractField;
use \jamwork\form\Button;

class AbstractFieldTest extends \PHPUnit_Framework_TestCase
{

	private $AbstractField = null;


	public function testRequired()
	{
		$obj = $this->AbstractField->required();
		$this->assertTrue($this->readAttribute($this->AbstractField, 'required'));
		$this->assertSame($obj, $this->AbstractField);
	}

	public function testLabel()
	{
		$obj = $this->AbstractField->label('testlabel');
		$this->assertSame('testlabel', $this->readAttribute($this->AbstractField, 'label'));
		$this->assertSame($obj, $this->AbstractField);
	}

	public function testName()
	{
		$obj = $this->AbstractField->name('testname');
		$this->assertSame('testname', $this->readAttribute($this->AbstractField, 'name'));
		$this->assertSame($obj, $this->AbstractField);
	}

	public function testValue()
	{
		$obj = $this->AbstractField->value('testvalue');
		$this->assertSame('testvalue', $this->readAttribute($this->AbstractField, 'value'));
		$this->assertSame($obj, $this->AbstractField);
	}

	public function testId()
	{
		$obj = $this->AbstractField->id(55);
		$this->assertSame(55, $this->readAttribute($this->AbstractField, 'id'));
		$this->assertSame($obj, $this->AbstractField);
	}

	public function testLabelRight()
	{
		$obj = $this->AbstractField->labelRight();
		$this->assertTrue($this->readAttribute($this->AbstractField, 'labelRight'));
		$this->assertSame($obj, $this->AbstractField);
	}


	public function testAddClass()
	{
		$obj = $this->AbstractField->addClass('test');
		$obj = $this->AbstractField->addClass('test1');

		$classes = $this->readAttribute($this->AbstractField, 'classes');
		$this->assertSame('test', $classes[0]);
		$this->assertSame('test1', $classes[1]);
		$this->assertSame($obj, $this->AbstractField);
	}

	public function testGetType()
	{

	}

	public function testIsRequired()
	{
		$this->assertFalse($this->AbstractField->isRequired());
		$this->AbstractField->required();
		$this->assertTrue($this->AbstractField->isRequired());
	}

	public function testGetLabel()
	{
		$this->assertSame('', $this->AbstractField->getLabel());
		$this->AbstractField->label('testlabel');
		$this->assertSame('testlabel', $this->AbstractField->getLabel());
	}

	public function testGetName()
	{
		$this->assertSame('', $this->AbstractField->getName());
		$this->AbstractField->name('testname');
		$this->assertSame('testname', $this->AbstractField->getName());
	}

	public function testGetValue()
	{
		$this->assertSame('', $this->AbstractField->getValue());
		$this->AbstractField->value('testvalue');
		$this->assertSame('testvalue', $this->AbstractField->getValue());
	}

	public function testGetId()
	{
		$this->assertSame('', $this->AbstractField->getId());
		$this->AbstractField->id(55);
		$this->assertSame(55, $this->AbstractField->getId());
	}

	public function testHasLabelRight()
	{
		$this->assertFalse($this->AbstractField->hasLabelRight());
		$this->AbstractField->labelRight();
		$this->assertTrue($this->AbstractField->hasLabelRight());
	}

	public function testGetClasses()
	{
		$this->assertSame('', $this->AbstractField->getClasses());

		$this->AbstractField->addClass('test');
		$this->AbstractField->addClass('test1');

		$this->assertSame('test test1', $this->AbstractField->getClasses());
	}

	public function testGetMarker()
	{
		$this->assertSame('#marker#', $this->AbstractField->getMarker());
	}

	public function testGetDataAttr()
	{
		$this->assertSame($this->AbstractField->getDataAttr(), '');

		$this->AbstractField->dataAttr('test1', 'testvalue1');
		$this->AbstractField->dataAttr('test2', 'testvalue2');

		//$data[] = 'data-'.$key.'="'.$value.'"';
		$strOutput = ' data-test1="testvalue1" data-test2="testvalue2"';
		$this->assertSame($this->AbstractField->getDataAttr(), $strOutput);

	}

	public function testDataAttr()
	{
		//wird in testGetDataAttr() getestet!
	}

	public function testPlaceholder()
	{
		$ret = $this->AbstractField->getPlaceholder();

		//leeres Array 
		$this->assertSame($ret, '');

		//Wert setzen; Return ist das eigene Objekt
		$this->assertInstanceOf('\jamwork\form\Button', $this->AbstractField->placeholder('test1'));

		$ret = $this->AbstractField->getPlaceholder();
		//Rückgabe String
		$this->assertSame($ret, 'test1');
	}

	public function testGetPlaceholder()
	{
		//wird in testPlaceholder() getestet!
	}

	protected function setUp()
	{
		$this->AbstractField = new Button('marker'); //Button um die abstracte Klasse zu testen
	}

	protected function tearDown()
	{
		unset($this->AbstractField);
	}
}
