<?php

namespace unittest\jamwork\template;

use \jamwork\template\FrontendController;

class FrontendControllerTest extends \PHPUnit_Framework_TestCase
{

	private $controller = false;

	public function testAssignCommandToSection()
	{
		$attr = $this->readAttribute($this->controller, 'sectionKeys');
		$this->assertEmpty($attr);
		$this->controller->assignCommandToSection('oof', 'foo');
		$attr = $this->readAttribute($this->controller, 'sectionKeys');
		$this->assertSame('foo', $attr['oof']);
	}

	public function testProzess()
	{
		$this->controller->assignCommandToSection('\module\xyCommand', 'navigation');
		$template = $this->getMockBuilder('jamwork\template\HtmlTemplate')->disableOriginalConstructor()->getMock();
		$response = $this->getMock('jamwork\common\HttpResponse');
		$section = $this->getMock('jamwork\template\HtmlSection');

		$response->expects($this->any())->method('getReturns')->will($this->returnValue(array(
		                                                                                     '\module\xyCommand' => 'returnOutput',
		                                                                                     '\module\asdfCommand' => 'returnOutput2'
		                                                                                )));

		$template->expects($this->any())->method('mainSection')->will($this->returnValue($section));

		$template->expects($this->any())->method('getSectionList')->will($this->returnValue(array('navigation' => 'navigation', 'main' => 'main')));

		$template->expects($this->any())->method('section')->will($this->returnValue($section));

		$template->expects($this->exactly(1))->method('getSectionList');
		$template->expects($this->exactly(1))->method('mainSection');
		$template->expects($this->exactly(1))->method('section');
		$response->expects($this->exactly(1))->method('getReturns');
		$section->expects($this->exactly(2))->method('append');
		$this->controller->prozess($template, $response);
	}

	protected function setUp()
	{
		$this->controller = new FrontendController();
	}

	protected function tearDown()
	{
		unset($this->controller);
	}
}
