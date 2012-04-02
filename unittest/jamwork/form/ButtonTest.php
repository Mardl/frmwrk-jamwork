<?php

namespace unittest\jamwork\form;

use jamwork\form\Button;

class ButtonTest extends \PHPUnit_Framework_TestCase
{
	private $checkbox = false;
	
	public function testGetType()
	{
		$type = $this->button->getType();
		$this->assertSame('button', $type);
	}
	
	public function testButtonType()
	{
		$attr = $this->readAttribute($this->button, 'buttonType');
		$this->assertSame($attr,'submit');
		
		$someText = 'irgend ein Text';
		
		$this->button->buttonType($someText);
		$attr = $this->readAttribute($this->button, 'buttonType');
		$this->assertSame($attr, $someText);
	}
	
	public function testGetButtonType()
	{
		$this->assertSame($this->button->getButtonType(),'submit');
		$this->button->buttonType('test');
		$this->assertSame($this->button->getButtonType(), 'test');
	}
	
	public function testText()
	{
		$attr = $this->readAttribute($this->button, 'text');
		$this->assertEmpty($attr);
		
		$someText = 'irgend ein Text';
		
		$this->button->text($someText);
		$attr = $this->readAttribute($this->button, 'text');
		$this->assertSame($attr, $someText);		
	}
	
	public function testGetText()
	{
		$this->assertEmpty($this->button->getText());
		$this->button->text('test');
		$this->assertSame($this->button->getText(), 'test');
	}
	
	
	/*public function testRequired()
	{
		$attr = $this->readAttribute($this->button, 'required');
		$this->assertFalse($attr);
		
		$this->button->required();
		$attr = $this->readAttribute($this->button, 'required');
		$this->assertTrue($attr);		
	}
	
	public function testLabel()
	{
		$attr = $this->readAttribute($this->button, 'label');
		$this->assertEmpty($attr);
		
		$someText = 'irgend ein Text';
		
		$this->button->label($someText);
		$attr = $this->readAttribute($this->button, 'label');
		$this->assertSame($attr, $someText);		
	}
	
	public function testName()
	{
		$attr = $this->readAttribute($this->button, 'name');
		$this->assertEmpty($attr);
		
		$someText = 'irgend ein Text';
		
		$this->button->name($someText);
		$attr = $this->readAttribute($this->button, 'name');
		$this->assertSame($attr, $someText);		
	}
	
	public function testValue()
	{
		$attr = $this->readAttribute($this->button, 'value');
		$this->assertEmpty($attr);
		
		$someText = 'irgend ein Text';
		
		$this->button->value($someText);
		$attr = $this->readAttribute($this->button, 'value');
		$this->assertSame($attr, $someText);		
	}
	
	public function testId()
	{
		$attr = $this->readAttribute($this->button, 'id');
		$this->assertEmpty($attr);
		
		$someText = 'irgend ein Text';
		
		$this->button->id($someText);
		$attr = $this->readAttribute($this->button, 'id');
		$this->assertSame($attr, $someText);		
	}
	
	public function testLabelRight()
	{
		$attr = $this->readAttribute($this->button, 'labelRight');
		$this->assertFalse($attr);
		
		$this->button->labelRight();
		$attr = $this->readAttribute($this->button, 'labelRight');
		$this->assertTrue($attr);		
	}
	
	public function testAddClass()
	{
		$attr = $this->readAttribute($this->button, 'classes');
		$this->assertEmpty($attr);
		$this->assertTrue(is_array($attr));
		
		$this->button->addClass('test-class');
		$attr = $this->readAttribute($this->button, 'classes');
		$this->assertNotEmpty($attr);
		$this->assertSame($attr, array(0 => 'test-class'));
	}
	
	public function testIsRequired()
	{
		$this->assertFalse($this->button->isRequired());
		$this->button->required();
		$this->assertTrue($this->button->isRequired());
	}
	
	public function testGetLabel()
	{
		$this->assertEmpty($this->button->getLabel());
		$this->button->label('test');
		$this->assertSame($this->button->getLabel(), 'test');
	}
	
	public function testGetName()
	{
		$this->assertEmpty($this->button->getName());
		$this->button->name('test');
		$this->assertSame($this->button->getName(), 'test');
	}
	
	public function testGetValue()
	{
		$this->assertEmpty($this->button->getValue());
		$this->button->value('test');
		$this->assertSame($this->button->getValue(), 'test');
	}
	
	public function testGetId()
	{
		$this->assertEmpty($this->button->getId());
		$this->button->id('test');
		$this->assertSame($this->button->getId(), 'test');
	}
	
	public function testHasLabelRight()
	{
		$this->assertFalse($this->button->hasLabelRight());
		$this->button->labelRight();
		$this->assertTrue($this->button->hasLabelRight());
	}
	
	public function testGetClasses()
	{
		$this->assertEmpty($this->button->getClasses());
		$this->button->addClass('test1');
		$this->assertSame($this->button->getClasses(), 'test1');
		$this->button->addClass('test2');
		$this->assertSame($this->button->getClasses(), 'test1 test2');
	}*/
	
	public function testGetFieldType()
	{
		$this->assertSame('button', $this->button->getFieldType());
	}
	
	protected function setUp()
	{
		$this->button = new Button();
	}
	
	protected function tearDown()
	{
		unset($this->button);
	} 
	
}