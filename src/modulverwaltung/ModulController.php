<?php

namespace jamwork\modulverwaltung;

class ModulController
{

	private $modulMap = array();
	private $directory = '';
	private $name = '';

	private $mapCountPrev = 0;
	private $mapCount = 1000;

	private $runPrev = array();

	public function __construct($dir, $root)
	{
		$this->directory = $root . $dir;
		$this->name = $dir;
	}

	public function __destruct()
	{
		foreach ($this->modulMap as $obj)
		{
			unset($obj);
		}
	}

	public function addPrev($namepace)
	{
		$this->runPrev[] = $namepace;
	}

	public function getModulObjects()
	{
		$this->readModule();
		ksort($this->modulMap);

		return $this->modulMap;
	}

	private function readModule()
	{
		if (!empty($this->modulMap))
		{
			return true;
		}

		$iterator = new \DirectoryIterator($this->directory);
		foreach ($iterator as $iteration)
		{
			if ($iteration->isDot())
			{
				continue;
			}

			if ($iteration->isDir())
			{
				$namespace = str_replace('../', '\\', $this->name) . $iteration->getBasename();
				$namespace = str_replace('/', '\\', $namespace);

				if (in_array($namespace, $this->runPrev))
				{

					$this->modulMap[array_search($namespace, $this->runPrev)] = new ModulObject($this->directory . $iteration->getBasename(), $namespace);
					continue;
				}
				$this->modulMap[$this->mapCount++] = new ModulObject($this->directory . $iteration->getBasename(), $namespace);
			}
		}
	}
}
