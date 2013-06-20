<?php

namespace unittest\jamwork\form;

use jamwork\form\Form;

class FormTest extends \PHPUnit_Framework_TestCase
{
	public function testName()
	{
		$attrempty = $this->readAttribute($this->form, 'name');
		$getempty = $this->form->getName();	
		$this->form->name('name');
		$attrfilled = $this->readAttribute($this->form, 'name');
		$getfilled = $this->form->getName();
		
		$this->assertEmpty($attrempty);
		$this->assertSame('name', $attrfilled);
		$this->assertsame($attrempty, $getempty);
		$this->assertsame($attrfilled, $getfilled);
	}
	
	public function testGetName()
	{
		// wird in testName getestet
	}
	
	public function testId()
	{
		$attrempty = $this->readAttribute($this->form, 'id');
		$getempty = $this->form->getId();	
		$this->form->id('id');
		$attrfilled = $this->readAttribute($this->form, 'id');
		$getfilled = $this->form->getId();
		
		$this->assertEmpty($attrempty);
		$this->assertSame('id', $attrfilled);
		$this->assertsame($attrempty, $getempty);
		$this->assertsame($attrfilled, $getfilled);
	}
	
	public function testGetId()
	{
		// wird in testId getestet
	}
	
	public function testClasses()
	{
		$attrempty = $this->readAttribute($this->form, 'classes');
		$getempty = $this->form->getClasses();	
		$this->form->addClass('classes');
		$attrfilled = $this->readAttribute($this->form, 'classes');
		$getfilled = $this->form->getClasses();
		$this->form->addClass('classes2');
		$this->form->addClass('classes3');
		$attrfilled_multi = $this->readAttribute($this->form, 'classes');
		$getfilled_multi = $this->form->getClasses();
		
		$this->assertEmpty($attrempty);
		$this->assertSame('classes', $attrfilled[0]);
		$this->assertEmpty($getempty);
		$this->assertsame($attrfilled[0], $getfilled);
		$this->assertSame($getfilled_multi, implode(' ', $attrfilled_multi));
	}
	
	public function testGetClasses()
	{
		// wird in testClasses getestet
	}
	
	public function testAddClass()
	{
		// wird in testAction getestet
	}
	
	public function testMethod()
	{
		$attrempty = $this->readAttribute($this->form, 'method');
		$getempty = $this->form->getMethod();	
		$this->form->method('method');
		$attrfilled = $this->readAttribute($this->form, 'method');
		$getfilled = $this->form->getMethod();
		
		$this->assertEmpty($attrempty);
		$this->assertSame('method', $attrfilled);
		$this->assertsame($attrempty, $getempty);
		$this->assertsame($attrfilled, $getfilled);
	}
	
	public function testGetMethod()
	{
		// wird in testMethod getestet
	}
	
	public function testAction()
	{
		$attrempty = $this->readAttribute($this->form, 'action');
		$getempty = $this->form->getAction();	
		$this->form->action('action');
		$attrfilled = $this->readAttribute($this->form, 'action');
		$getfilled = $this->form->getAction();
		
		$this->assertEmpty($attrempty);
		$this->assertSame('action', $attrfilled);
		$this->assertsame($attrempty, $getempty);
		$this->assertsame($attrfilled, $getfilled);
	}
	
	public function testGetAction()
	{
		// wird in testAction getestet
	}
	
	public function testEnctype()
	{
		$attrempty = $this->readAttribute($this->form, 'enctype');
		$getempty = $this->form->getEnctype();	
		$this->form->enctype('enctype');
		$attrfilled = $this->readAttribute($this->form, 'enctype');
		$getfilled = $this->form->getEnctype();
		
		$this->assertEmpty($attrempty);
		$this->assertSame('enctype', $attrfilled);
		$this->assertsame($attrempty, $getempty);
		$this->assertsame($attrfilled, $getfilled);
	}
	
	public function testGetEnctype()
	{
		//wird in test Enctype getestet
	}
	
	protected function setUp()
	{
		$this->form = new Form();
	}
	
	protected function tearDown()
	{
		unset($this->form);
	} 
	
		
}