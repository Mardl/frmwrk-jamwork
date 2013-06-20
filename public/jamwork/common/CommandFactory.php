<?php

namespace jamwork\common;

class CommandFactory
{
	private $commands = array();
	
	public function addCommand(Command $command)
	{
		$this->commands[] = $command;
	}
	
	public function run()
	{	
		$registry = Registry::getInstance();
		$request = $registry->getRequest();
		$response = $registry->getResponse();
			
		foreach($this->commands as $command)
		{
			$command->execute($request, $response);
		}
	}
}
