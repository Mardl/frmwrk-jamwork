<?php

namespace jamwork\common;

use \jamwork\modulverwaltung\ModulController;
use \jamwork\common\Registry;

class AutoCommandResolver extends FileSystemCommandResolver
{

	private $factory = null;
	private $resolver = null;
	private $basePath = null;
	private $root = '/';

	public function __construct(CommandFactory $factory, $basePath)
	{
		$this->factory = $factory;
		$this->basePath = $basePath;
	}

	public function run($dir)
	{
		$commands = $this->getCommandArray($dir);
		foreach ($commands as $command)
		{
			$cmd = $this->getCommandInstance($command);
			$this->factory->addCommand($cmd);
		}
	}

	private function getCommandArray($dir)
	{
		$ret = array();

		$modulController = new ModulController($dir, $this->basePath);
		$modulObjects = $modulController->getModulObjects();
		foreach ($modulObjects as $pathname => $obj)
		{
			$ret = array_merge($ret, $obj->getCommands());
		}

		return $ret;
	}
}
