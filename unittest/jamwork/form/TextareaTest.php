<?php

namespace unittest\jamwork\form;

use jamwork\form\Textarea;

class TextareaTest extends \PHPUnit_Framework_TestCase
{
	
	private $textarea = null;
	
	public function testRequired()
	{
		$attrfalse = $this->readAttribute($this->textarea, 'required');
		$getfalse = $this->textarea->isRequired();	
		$this->textarea->required();
		$attrtrue = $this->readAttribute($this->textarea, 'required');
		$gettrue = $this->textarea->isRequired();
			
		$this->assertFalse($attrfalse);
		$this->assertTrue($attrtrue);
		$this->assertsame($attrfalse, $getfalse);
		$this->assertsame($attrtrue, $gettrue);
	}
	
	public function testIsRequired()
	{
		//siehe testRequired
	}
	
	public function testLabel()
	{
		$attrempty = $this->readAttribute($this->textarea, 'label');
		$getempty = $this->textarea->getLabel();	
		$this->textarea->label('Label');
		$attrfilled = $this->readAttribute($this->textarea, 'label');
		$getfilled = $this->textarea->getLabel();
		
		$this->assertEmpty($attrempty);
		$this->assertSame('Label', $attrfilled);
		$this->assertsame($attrempty, $getempty);
		$this->assertsame($attrfilled, $getfilled);
	}
	
	public function testGetLabel()
	{
		//siehe test Label
	}
	
	public function testName()
	{
		$attrempty = $this->readAttribute($this->textarea, 'name');
		$getempty = $this->textarea->getname();	
		$this->textarea->name('Name');
		$attrfilled = $this->readAttribute($this->textarea, 'name');
		$getfilled = $this->textarea->getName();
		
		$this->assertEmpty($attrempty);
		$this->assertSame('Name', $attrfilled);
		$this->assertsame($attrempty, $getempty);
		$this->assertsame($attrfilled, $getfilled);
	}
	
	public function testGetName()
	{
		//siehe testName
	}
	
	public function testValue()
	{
		$attrempty = $this->readAttribute($this->textarea, 'value');
		$getempty = $this->textarea->getValue();	
		$this->textarea->value('Value');
		$attrfilled = $this->readAttribute($this->textarea, 'value');
		$getfilled = $this->textarea->getValue();
		
		$this->assertEmpty($attrempty);
		$this->assertSame('Value', $attrfilled);
		$this->assertsame($attrempty, $getempty);
		$this->assertsame($attrfilled, $getfilled);
	}
	
	public function testGetValue()
	{
		//siehe test Value
	}
	
	public function testId()
	{
		$attrempty = $this->readAttribute($this->textarea, 'id');
		$getempty = $this->textarea->getId();	
		$this->textarea->id('id');
		$attrfilled = $this->readAttribute($this->textarea, 'id');
		$getfilled = $this->textarea->getId();
		
		$this->assertEmpty($attrempty);
		$this->assertSame('id', $attrfilled);
		$this->assertsame($attrempty, $getempty);
		$this->assertsame($attrfilled, $getfilled);
	}
	
	public function testGetId()
	{
		//siehe testId
	}
	
	public function testLabelRight()
	{
		$attrfalse = $this->readAttribute($this->textarea, 'labelRight');
		$getfalse = $this->textarea->hasLabelRight();	
		$this->textarea->labelRight();
		$attrtrue = $this->readAttribute($this->textarea, 'labelRight');
		$gettrue = $this->textarea->hasLabelRight();
			
		$this->assertFalse($attrfalse);
		$this->assertTrue($attrtrue);
		$this->assertsame($attrfalse, $getfalse);
		$this->assertsame($attrtrue, $gettrue);
	}
	
	public function testHasLabelRight()
	{
		//siehe testLabelRight
	}
	
	public function testReadOnly()
	{
		$attrfalse = $this->readAttribute($this->textarea, 'readOnly');
		$getfalse = $this->textarea->isReadOnly();	
		$this->textarea->readOnly();
		$attrtrue = $this->readAttribute($this->textarea, 'readOnly');
		$gettrue = $this->textarea->isReadOnly();
			
		$this->assertFalse($attrfalse);
		$this->assertTrue($attrtrue);
		$this->assertsame($attrfalse, $getfalse);
		$this->assertsame($attrtrue, $gettrue);
	}
	
	public function testIsReadOnly()
	{
		// wird indirekt getestet
	}
	
	public function testAddClass()
	{
		$attrempty = $this->readAttribute($this->textarea, 'classes');
		$getempty = $this->textarea->getClasses();	
		$this->textarea->addClass('classes');
		$attrfilled = $this->readAttribute($this->textarea, 'classes');
		$getfilled = $this->textarea->getClasses();
		$this->textarea->addClass('classes2');
		$this->textarea->addClass('classes3');
		$attrfilled_multi = $this->readAttribute($this->textarea, 'classes');
		$getfilled_multi = $this->textarea->getClasses();
		
		$this->assertEmpty($attrempty);
		$this->assertSame('classes', $attrfilled[0]);
		$this->assertEmpty($getempty);
		$this->assertsame($attrfilled[0], $getfilled);
		$this->assertSame($getfilled_multi, implode(' ', $attrfilled_multi));
	}
	
	public function testGetClasses()
	{
		//siehe teastAddClass
	}
	
	public function testGetType()
	{
		$type = $this->textarea->getType();
		$this->assertSame('textarea', $type);
	}
	

	
	public function testGetFieldType()
	{
		$this->assertSame('textarea', $this->textarea->getFieldType());
	}
	
	public function testGetMarker()
	{
		//wird in AbstractFieldTest getestet!
	}
	
	protected function setUp()
	{
		$this->textarea = new Textarea();
	}
	
	protected function tearDown()
	{
		unset($this->textarea);
	} 
	
		
}