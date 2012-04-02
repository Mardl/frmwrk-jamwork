<?php

namespace unittest;

class AllTest extends SingleTest
{

	public function __construct($dir)
	{
		parent::__construct($dir);
		
		$this->searchTests($dir);
	}
	
	private function searchTests($dir)
	{
		$directory = dirname(__FILE__).$dir;
		$iterator = new \DirectoryIterator($directory);
		
		foreach($iterator as $iteration)
		{
			if($iteration->isFile())
			{
				$namespace = str_replace('/jamwork', 'unittest\jamwork', $dir);
				$namespace = str_replace('/', '\\', $namespace);
				$className = $namespace.$iteration->getBasename('.php');
				if (class_exists($className, true))
				{
					$this->getSuite()->addTestSuite($className);
				}				
			}
			elseif(!$iteration->isDot())
			{
				$this->searchTests($dir.$iteration->getFilename().'/');
			}
		}
	}
}