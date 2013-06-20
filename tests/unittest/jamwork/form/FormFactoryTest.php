<?php

namespace unittest\jamwork\form;

use jamwork\form\FormFactory;
use jamwork\form\FormHTML;

class FormFactoryTest extends \PHPUnit_Framework_TestCase
{
	private $formFactory = null;
	private $formFactory2 = null;
	private $formHTML = null;
	
	public function testFieldset()
	{	
		$field = $this->formFactory->fieldset();
		$comparray['0-1'] = $field;
		
		$this->assertInstanceOf('jamwork\form\FormFactory', $field);
		$this->assertAttributeEquals($comparray, 'array', $this->formFactory);	
	}
	
	public function testTextfield()
	{
		$field = $this->formFactory->textfield();
		$comparray['0-1'] = $field;
		
		$this->assertInstanceOf('jamwork\form\Textfield', $field);
		$this->assertAttributeEquals($comparray, 'array', $this->formFactory);	
	}
	
	public function testTextarea()
	{
		$field = $this->formFactory->textarea();
		$comparray['0-1'] = $field;
		
		$this->assertInstanceOf('jamwork\form\Textarea', $field);
		$this->assertAttributeEquals($comparray, 'array', $this->formFactory);	
	}
	
	public function testSelect()
	{
		$field = $this->formFactory->select();
		$comparray['0-1'] = $field;
		
		$this->assertInstanceOf('jamwork\form\Select', $field);
		$this->assertAttributeEquals($comparray, 'array', $this->formFactory);	
	}
	
	public function testRadiobutton()
	{
		$field = $this->formFactory->Radiobutton();
		$comparray['0-1'] = $field;
		
		$this->assertInstanceOf('jamwork\form\Radiobutton', $field);
		$this->assertAttributeEquals($comparray, 'array', $this->formFactory);	
	}
	
	public function testPassword()
	{
		$field = $this->formFactory->password();
		$comparray['0-1'] = $field;
		
		$this->assertInstanceOf('jamwork\form\Password', $field);
		$this->assertAttributeEquals($comparray, 'array', $this->formFactory);	
	}
	
	public function testHidden()
	{
		$field = $this->formFactory->hidden();
		$comparray['0-1'] = $field;
		
		$this->assertInstanceOf('jamwork\form\Hidden', $field);
		$this->assertAttributeEquals($comparray, 'array', $this->formFactory);	
	}
	
	public function testCheckbox()
	{
		$field = $this->formFactory->checkbox();
		$comparray['0-1'] = $field;
		
		$this->assertInstanceOf('jamwork\form\Checkbox', $field);
		$this->assertAttributeEquals($comparray, 'array', $this->formFactory);	
	}
	
	public function testButton()
	{
		$field = $this->formFactory->button();
		$comparray['0-1'] = $field;
		
		$this->assertInstanceOf('jamwork\form\Button', $field);
		$this->assertAttributeEquals($comparray, 'array', $this->formFactory);	
	}
	
	public function testPtag()
	{
		$field = $this->formFactory->ptag();
		$comparray['0-1'] = $field;
		
		$this->assertInstanceOf('jamwork\form\Ptag', $field);
		$this->assertAttributeEquals($comparray, 'array', $this->formFactory);	
	}
	
	public function testFilefield()
	{
		$field = $this->formFactory->filefield();
		$comparray['0-1'] = $field;
		
		$this->assertInstanceOf('jamwork\form\Filefield', $field);
		$this->assertAttributeEquals($comparray, 'array', $this->formFactory);	
	}
	
	public function testForm()
	{
		$field = $this->formFactory->form();
		$compare = $field;
		
		$this->assertInstanceOf('jamwork\form\Form', $field);
		$this->assertAttributeEquals($compare, 'form', $this->formFactory);	
	}
	
	public function testCreate()
	{
		$this->formFactory->form();	
		$str = $this->formFactory->create();
		$this->assertContains('<form method="" action="" name="" class="" id=""', $str);
		$this->assertContains('</form>', $str);
	}
	
	public function testCreate_MockGenerate()
	{
		$formHTML = $this->getMock('jamwork\form\FormHTML');	
		$formHTML->expects($this->exactly(1))->method('generate');
		
		$formFactory =  $this->getMock('jamwork\form\FormFactory',array('generate'),array($formHTML));	
		$formFactory->form();
		$formFactory->expects($this->exactly(1))->method('generate');
		$formFactory->create();		
	}
	
	public function testGenerate_MockGenerateStr()
	{
		$formFactory =  $this->getMock('jamwork\form\FormFactory',array('generateStr'),array(),'',false);	
		$field = $formFactory->textfield();
		$field = $formFactory->textfield();
		
		$formFactory->expects($this->exactly(2))->method('generateStr');
		$formFactory->generate();		
	}
	
	public function testGenerateStr_MockGenerateFieldset()
	{
		$formHTML = $this->getMock('jamwork\form\FormHTML');	
		$formHTML->expects($this->exactly(1))->method('generateFieldset');
		
		$formFactory = new FormFactory($formHTML);
		$field = $formFactory->fieldset();

		$formFactory->generateStr($field);		 
	}
	
	public function testGenerateStr_MockGenerateFormField()
	{
		$formHTML = $this->getMock('jamwork\form\FormHTML');	
		$formHTML->expects($this->exactly(1))->method('generateFormField');
		
		$formFactory = new FormFactory($formHTML);
		$field = $formFactory->Textfield();

		$formFactory->generateStr($field);
	}
	
	public function test__toString()
	{
		$str = $this->formFactory->__toString();
		$this->assertSame($str, '');
	}
	
	public function testAddClass()
	{
		$attr = $this->readAttribute($this->formFactory, 'classes');
		$this->assertEmpty($attr);
		
		$class = 'FOO';
		$formFactory = $this->formFactory->addClass($class);	
	
		$attr = $this->readAttribute($this->formFactory, 'classes');
		$this->assertSame($class, $attr[0]);
		$this->assertInstanceOf('jamwork\form\FormFactory', $formFactory);
	}
	
	public function testGetClasses()
	{
		$attr = $this->formFactory->getClasses();
		$this->assertEmpty($attr);
		
		$class1 ='FOO';
		$class2 = 'OOF';
		
		$this->formFactory->addClass($class1);
		$this->formFactory->addClass($class2);		
	
		$attr = $this->formFactory->getClasses();
		$this->assertSame($class1.' '.$class2, $attr);
	}
	
	public function testLegend()
	{
		$attr = $this->readAttribute($this->formFactory, 'legend');
		$this->assertEmpty($attr);
		
		$legend = 'FOO';
		$formFactory = $this->formFactory->legend($legend);	
	
		$attr = $this->readAttribute($this->formFactory, 'legend');
		$this->assertSame($legend, $attr);
		$this->assertInstanceOf('jamwork\form\FormFactory', $formFactory);
	}
	
	public function testGetLegend()
	{
		$attr = $this->formFactory->getLegend();
		$this->assertEmpty($attr);
		
		$legend = 'FOO';
		$this->formFactory->legend($legend);	
	
		$attr = $this->formFactory->getLegend();
		$this->assertSame($legend, $attr);
	}
	
	public function testIssetLegend()
	{
		$this->assertFalse($this->formFactory->issetLegend());
		
		$legend = 'FOO';
		$formFactory = $this->formFactory->legend($legend);	
		$this->assertTrue($this->formFactory->issetLegend());
	}
	
	
	public function testNextMarkerPrefix()
	{
		$method = new \ReflectionMethod(
          $this->formFactory2, 'nextMarkerPrefix'
        );
 		
	    $method->setAccessible(TRUE);		
		$this->assertSame(6,$method->invoke($this->formFactory2));
	}
	
	public function testNextCounter()
	{
		$method = new \ReflectionMethod(
          $this->formFactory, 'nextCounter'
        );
 		
	    $method->setAccessible(TRUE);		
		$this->assertSame(1,$method->invoke($this->formFactory));
		$this->assertSame(2,$method->invoke($this->formFactory));
	}
	
	public function testNextMarker()
	{
		$method = new \ReflectionMethod(
          $this->formFactory2, 'nextMarker'
        );
 		
	    $method->setAccessible(TRUE);		
		$this->assertSame('5-1',$method->invoke($this->formFactory2));
		$this->assertSame('5-2',$method->invoke($this->formFactory2));
	}
	
	public function testGetMarker()
	{
		$this->assertSame('#mark#',$this->formFactory2->getMarker());
	}
	
	public function testContent()
	{
		$this->formFactory->content('test');
		$this->assertSame('test',$this->readAttribute($this->formFactory, 'content'));
	}
	
	public function testReplaceMarker()
	{
		$method = new \ReflectionMethod(
          $this->formFactory2, 'replaceMarker'
        );
 		
	    $method->setAccessible(TRUE);	
	
		//Textfeld anlegen
		$txtfield = $this->formFactory2->textfield();
		//Content setzen		
		$this->formFactory2->content('test #5-1# test');
		
		$cnt = 	$this->readAttribute($this->formFactory2, 'content');
		$arr = 	$this->readAttribute($this->formFactory2, 'array');
		
		$this->assertSame('test <span class="formBlock text"><input type="text" value="" name="" id="" maxlength="" /></span> test',$method->invoke($this->formFactory2,$cnt,$arr));
	}
	
	public function testGetArray()
	{
		$obj = $this->formFactory->getArray();
		$this->assertSame(0,count($obj));
	}
	
	public function testGetDataAttr()
	{
		$this->assertSame($this->formFactory->getDataAttr(),'');
		
		$this->formFactory->dataAttr('test1', 'testvalue1');
		$this->formFactory->dataAttr('test2', 'testvalue2');
		
		//$data[] = 'data-'.$key.'="'.$value.'"';
		$strOutput = ' data-test1="testvalue1" data-test2="testvalue2"';
		$this->assertSame($this->formFactory->getDataAttr(),$strOutput);
		
	}
	
	public function testDataAttr()
	{
		//wird in testGetDataAttr() getestet!
	}
	
	protected function setUp()
	{
		$this->formHTML = new FormHTML();
		$this->formFactory = new FormFactory($this->formHTML);
		$this->formFactory2 = new FormFactory($this->formHTML,5,'mark');
	}
	
	protected function tearDown()
	{
		unset($this->formFactory);
		unset($this->formHTML);
	}
			
}