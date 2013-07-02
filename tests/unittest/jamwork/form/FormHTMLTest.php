<?php

namespace unittest\jamwork\form;

use jamwork\form\FormHTML;
use jamwork\form\Textfield;
use jamwork\form\Checkbox;
use jamwork\form\Textarea;
use jamwork\form\Password;
use jamwork\form\Hidden;
use jamwork\form\Select;
use jamwork\form\Button;
use jamwork\form\Form;

use jamwork\common\Registry;
use jamwork\common\HttpRequest;

class FormHTMLTest extends \PHPUnit_Framework_TestCase
{

	private $field = false;

	public function testGenerate()
	{
		$form = new Form();
		$form->method('FOO')->action('FOO');
		$string = $this->form->generate('FOO', $form);
		$this->assertSame('<form method="FOO" action="FOO" name="" class="" id="" enctype="">FOO</form>', $string);
	}

	public function testGenerateFieldset()
	{
		$formFactory = $this->getMock('jamwork\form\FormFactory', array('generate'), array($this->form));
		$formFactory->expects($this->exactly(1))->method('generate');

		$string = $this->form->generateFieldset($formFactory);
		$this->assertSame('<fieldset></fieldset>', $string);
	}

	public function testGenerateFormField_Textfield()
	{
		$this->field = new Textfield();

		$string = $this->form->generateFormField($this->field);
		$this->assertStringMatchesFormat($this->span('<input type="text" value="%A" name="%A" id="%A" maxlength="%A" />'), $string);
	}

	public function testGenerateFormField_Checkbox()
	{
		$this->field = new Checkbox();

		$string = $this->form->generateFormField($this->field);
		$this->assertStringMatchesFormat($this->span('<input type="checkbox" value="%A" name="%A" id="%A" %A />'), $string);
		$this->assertFalse($this->field->isChecked());
	}

	public function testGenerateFormField_Textarea()
	{
		$this->field = new Textarea();

		$string = $this->form->generateFormField($this->field);
		$this->assertStringMatchesFormat($this->span('<textarea name="%A" id="%A">%A</textarea>'), $string);
	}

	public function testGenerateFormField_Password()
	{
		$this->field = new Password();

		$string = $this->form->generateFormField($this->field);
		$this->assertStringMatchesFormat($this->span('<input type="password" value="%A" name="%A" id="%A" maxlength="%A" />'), $string);
	}

	public function testGenerateFormField_Hidden()
	{
		$this->field = new Hidden();

		$string = $this->form->generateFormField($this->field);
		$this->assertStringMatchesFormat('<input type="hidden" value="%A" name="%A" id="%A" maxlength="%A" />', $string);
	}

	public function testGenerateFormField_Select()
	{
		$this->field = new Select();

		$this->field->name('testselect')->id(12);

		$option = $this->field->newOption();
		$option->text('Text1')->value('TextA');

		$option = $this->field->newOption();
		$option->text('Text2')->value('TextB');

		$string = $this->form->generateFormField($this->field);
		$this->assertStringMatchesFormat($this->span('<select name="testselect" id="12" ><option value="TextA">Text1</option><option value="TextB">Text2</option></select>'), $string);
	}

	public function testGenerateFormField_Label()
	{
		$this->field = new Textfield();
		$this->field->label('LABEL');

		$string = $this->form->generateFormField($this->field);
		$this->assertStringMatchesFormat($this->span('<label for="">LABEL</label><input type="text" value="%A" name="%A" id="%A" maxlength="%A" />'), $string);

		$this->field = new Textfield();
		$this->field->label('LABEL');
		$this->field->labelRight();

		$string = $this->form->generateFormField($this->field);
		$this->assertStringMatchesFormat($this->span('<input type="text" value="%A" name="%A" id="%A" maxlength="%A" /><label for="">LABEL</label>'), $string);
	}

	public function testRequired_true()
	{
		$this->field = new Textfield();
		$this->field->name('foo')->required();

		$registry = Registry::getInstance();
		$request = new HttpRequest(array('foo' => 'foo'), array(), array(), array());
		$registry->setRequest($request);

		$method = new \ReflectionMethod($this->form, 'required');
		$method->setAccessible(true);
		$required = $method->invoke($this->form, $this->field);
		$this->assertTrue($required);
	}

	public function testRequired_falseNotRequired()
	{
		$this->field = new Textfield();

		$registry = Registry::getInstance();
		$request = new HttpRequest(array('foo' => 'foo'), array(), array(), array());
		$registry->setRequest($request);

		$method = new \ReflectionMethod($this->form, 'required');
		$method->setAccessible(true);
		$required = $method->invoke($this->form, $this->field);
		$this->assertFalse($required);
	}


	public function testRequired_falseValueGiven()
	{
		$this->field = new Textfield();
		$this->field->name('foo')->value('foo')->required();

		$registry = Registry::getInstance();
		$request = new HttpRequest(array('foo' => 'foo'), array(), array(), array());
		$registry->setRequest($request);

		$method = new \ReflectionMethod($this->form, 'required');
		$method->setAccessible(true);
		$required = $method->invoke($this->form, $this->field);
		$this->assertFalse($required);
	}

	public function testGenerateFormField_Button()
	{
		$this->field = new Button();
		$string = $this->form->generateFormField($this->field);
		$this->assertStringMatchesFormat($this->span('<button name="%A" type="%A" value="%A">%A</button>'), $string);
	}

	public function testGetPlaceholder()
	{
		$button = new Button();

		$method = new \ReflectionMethod($this->form, 'getPlaceholder');
		$method->setAccessible(true);
		$ret = $method->invoke($this->form, $button);

		//noch nicht gesetzt, darum muss hier '' zurück kommen! 
		$this->assertSame($ret, '');

		//Wert setzen; Return ist das eigene Objekt
		$this->assertInstanceOf('\jamwork\form\Button', $button->placeholder('test1'));

		$ret = $method->invoke($this->form, $button);
		//Rückgabe String 
		$this->assertSame($ret, ' placeholder="test1"');
	}

	protected function setUp()
	{
		$this->form = new FormHTML();
	}

	protected function tearDown()
	{
		unset($this->form);
		unset($this->field);
	}

	protected function span($str)
	{
		$spanstart = '<span class="formBlock ' . $this->field->getType() . '">';
		$spanend = '</span>';
		$str = $spanstart . $str . $spanend;

		return $str;
	}
}
	