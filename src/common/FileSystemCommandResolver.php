<?php

namespace jamwork\common;

class FileSystemCommandResolver implements CommandResolver
{

	private $noPermission = '';
	private $noLogin = '';

	public function getCommand(Request $request)
	{
		if ($request->hasParameter('cmd'))
		{
			$cmdName = $request->getParameter('cmd');

			return $this->getCommandInstance($cmdName);
		}

		return false;
	}

	public function setNoPermission($cmdName)
	{
		$this->noPermission = $cmdName;
	}

	public function getCommandInstance($cmdName)
	{
		$command = $this->loadCommand($cmdName);
		if ($command instanceof Command)
		{
			return $command;
		}
		throw new \Exception("Klasse {$cmdName} keine Instance von Command.");
	}

	private function loadCommand($cmdNameSpace)
	{
		try
		{
			$name = $cmdNameSpace . 'Command';
			$command = new $name();

			$registry = \jamwork\common\Registry::getInstance();
			$eventsDispatcher = $registry->getEventDispatcher();

			$event = $eventsDispatcher->triggerEvent('onCommandLoad', $this, $command);

			if ($event->isCanceled())
			{
				$name = $this->noPermission . 'Command';
				$command = new $name();
			}

			return $command;
		} catch (\Exception $e)
		{
			throw new \Exception("Command {$cmdNameSpace} existiert nicht. | " . $e->getMessage());
		}
	}
}
