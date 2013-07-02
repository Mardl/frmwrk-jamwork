<?php

namespace unittest\jamwork\common;

use jamwork\common\CommandFactory;
use unittest\module\UnittestCommand;
use jamwork\common\Registry;

class CommandFactoryTest extends \PHPUnit_Framework_TestCase
{

	private $commandFactory = null;

	public function testAddCommand()
	{
		$this->assertAttributeEquals(array(), 'commands', $this->commandFactory);

		$command = new UnittestCommand();
		$this->commandFactory->addCommand($command);

		$soll[] = $command;

		$this->assertAttributeEquals($soll, 'commands', $this->commandFactory);

	}

	public function testRun()
	{
		$getpost = array();
		$server = array();
		$cookie = array();

		$mockRequest = $this->getMock('jamwork\common\HttpRequest', array(), array($getpost, $server, $cookie, array()));
		$mockResponse = $this->getMock('jamwork\common\HttpResponse');

		$registry = Registry::getInstance();
		$registry->setRequest($mockRequest);
		$registry->setResponse($mockResponse);

		$mockCommand = $this->getMock('unittest\module\UnittestCommand');
		$mockCommand->expects($this->once())->method('execute');

		$mockCommand1 = $this->getMock('unittest\module\UnittestCommand');
		$mockCommand1->expects($this->once())->method('execute');

		$this->commandFactory->addCommand($mockCommand);
		$this->commandFactory->addCommand($mockCommand1);

		$this->commandFactory->run();
		$registry->reset();

	}

	protected function setUp()
	{
		$this->commandFactory = new CommandFactory();
	}

	protected function tearDown()
	{
		unset($this->commandFactory);
	}
}
	