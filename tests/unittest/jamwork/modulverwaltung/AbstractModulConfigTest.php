<?php

namespace unittest\jamwork\modulverwaltung;

use \jamwork\modulverwaltung\AbstractModulConfig;


class AbstractModulConfigTest extends \PHPUnit_Framework_TestCase
{

	private $ModulConfig = null;

	public function testGetVersion()
	{
		$this->assertSame($this->ModulConfig->getVersion(), '1.0.0');
	}

	public function testGetName()
	{
		$this->assertSame($this->ModulConfig->getName(), 'Testmodul1');
	}

	public function testGetShow()
	{
		$this->assertSame($this->ModulConfig->getShow(), 1);
	}

	public function testGetSort()
	{
		$this->assertSame($this->ModulConfig->getSort(), 1);
	}

	public function testIsVersionLower()
	{
		//Version ist kleiner
		$this->assertTrue($this->ModulConfig->isVersionLower('1.0.0', '1.5.5'));
		$this->assertTrue($this->ModulConfig->isVersionLower('1.5.0', '1.5.5'));
		$this->assertTrue($this->ModulConfig->isVersionLower('1.5.4', '1.5.5'));

		//Version gleich, also nicht kleiner
		$this->assertFalse($this->ModulConfig->isVersionLower('1.5.5', '1.5.5'));

		//Version ist größer
		$this->assertFalse($this->ModulConfig->isVersionLower('1.5.6', '1.5.5'));
		$this->assertFalse($this->ModulConfig->isVersionLower('1.6.0', '1.5.5'));
		$this->assertFalse($this->ModulConfig->isVersionLower('2.0.0', '1.5.5'));
	}

	protected function setUp()
	{
		$this->ModulConfig = new \unittest\module\testmodul1\Testmodul1Config();
	}

	protected function tearDown()
	{
		unset($this->ModulConfig);
	}
}
