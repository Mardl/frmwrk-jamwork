<?php

namespace jamwork\modulverwaltung;

use jamwork\modulverwaltung\AbstractModulConfig;

class ModulObject
{

	private $namespace = '';
	private $path = '';
	private $commands = array();
	private $configFile = '';
	private $config = '';
	private $dependencies = array();
	private $configObj = null;

	public function __construct($path, $namespace)
	{
		$this->setPath($path);
		$this->setNamespace($namespace);

		$this->readFiles();

		$this->newConfig($this->config);
	}

	public function __destruct()
	{
		unset($this->configObj);
	}

	private function newConfig($config)
	{
		if (class_exists($config))
		{
			$this->setConfig(new $config());
		}
		else
		{
			throw new \Exception('Keine Config-Klasse (' . $config . ') im Modul: ');
		}
	}

	private function setConfig(AbstractModulConfig $config)
	{
		$this->configObj = $config;
	}

	private function setPath($path)
	{
		if (substr($path, -1) != '/')
		{
			$path .= '/';
		}
		$this->path = $path;
	}

	private function setNamespace($ns)
	{
		$this->namespace = $ns;
	}

	public function getNamespace()
	{
		return $this->namespace;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getVersion()
	{
		if ($this->configObj->getVersion())
		{
			return $this->configObj->getVersion();
		}
		else
		{
			throw new \Exception("Konstante Version " . $this->getNamespace() . " ist nicht gesetzt!");
		}
	}

	public function getClearName()
	{
		if ($this->configObj->getName())
		{
			return $this->configObj->getName();
		}
		else
		{
			throw new \Exception("Konstante Name " . $this->getNamespace() . " ist nicht gesetzt!");
		}
	}

	private function readFiles()
	{

		$iterator = new \DirectoryIterator($this->getPath());
		foreach ($iterator as $iteration)
		{
			if ($iteration->isFile())
			{
				$name = substr($iteration->getBasename(), -11);
				if ($name == 'Command.php')
				{
					$this->commands[$iteration->getBasename()] = str_replace($name, '', $this->getNamespace() . '\\' . $iteration->getBasename());
					continue;
				}

				$name = substr($iteration->getBasename(), -10);
				if ($name == 'Config.php')
				{
					$this->configFile = $iteration->getBasename();
					$this->config = $this->getNamespace() . '\\' . $iteration->getBasename('.php');
					continue;
				}
			}
		}
	}

	public function getCommands()
	{
		return $this->commands;
	}

	public function getShow()
	{
		return $this->configObj->getShow();
	}

	public function getSort()
	{
		return $this->configObj->getSort();
	}

	public function install()
	{
		return $this->configObj->install();
	}

	public function update($dbVersion)
	{
		return $this->configObj->update($dbVersion);
	}

	public function isVersionLower($db, $modul)
	{
		return $this->configObj->isVersionLower($db, $modul);
	}

	public function getCommandInfo($cmd, $key)
	{
		$command = $cmd . 'Command';
		$key = strtolower($key);

		switch ($key)
		{
			case 'name':
				return defined("$command::NAME") ? $command::NAME : '';
				break;
			case 'sort':
				return defined("$command::SORT") ? $command::SORT : 0;
				break;
			case 'show':
				return defined("$command::SHOW") ? $command::SHOW : 0;
				break;
			case 'navi':
				return defined("$command::NAVI") ? $command::NAVI : 0;
				break;
		}

		return '';
	}
}
	