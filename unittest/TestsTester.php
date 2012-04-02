<?php

class TestsTester
{
	private $testFolder = '';
	private $classFolder = '';
	
	private $excludeFiles = array();
	private $excludeMethods = array('*::__construct', '*::__destruct');
	
	private $testClasses = array();
	private $liveClasses = array();
	private $ignorePath = array();
	
	private $testMethods = array();
	private $testMethodsWithUnderscore = array();
	private $liveMethods = array();
	
	private $noTestFile = array();
	private $noTestMethod = array();
	
	private $shownMethods = array();
	
	public function __construct($testFolder, $classFolder,array $ignorePath = array())
	{
		$this->testFolder = $testFolder;
		$this->classFolder = $classFolder;
		$this->ignorePath = $ignorePath;
		
		if(isset($_GET['createTest']) && !empty($_GET['createTest']))
		{
			$this->createTest($_GET['createTest']);
		}
	}
	
	public function excludeFile($file)
	{
		$this->excludeFiles[] = $file;
	}
	
	public function excludeMethod($name)
	{
		$this->excludeMethods[] = $name;
	}
	
	private function readPhpFiles()
	{
		$this->testClasses = $this->getPhpFiles($this->testFolder, array(), 'unittest/');		
		$this->liveClasses = $this->getPhpFiles($this->classFolder);
	}
	
	public function run()
	{
		$this->readPhpFiles();
		
		$this->checkClasses();
		$this->checkMethods();
	}
	
	public function flush()
	{
		$this->run();
		
		echo "<pre>";
		
		$noOutput = true;
		
		if(!empty($this->noTestFile))
		{
			echo "Folgende Klassen haben keine Test-Klasse:\n";
			echo "-----------------------------------------\n";
			foreach($this->noTestFile as $namespace => $classname)
			{
				echo " - ".str_pad($classname, 35, '.').str_pad($namespace, 85)."<a href=\"?createTest=".$namespace."\" class='testanlegen'>anlegen</a>\n";
			}
			echo "\n";
			echo "\n";
			
			$noOutput = false;
		}
		
		if(!empty($this->noTestMethod))
		{
			echo "Folgende Methoden werden nicht gestest:\n";
			echo "---------------------------------------\n";
			foreach($this->noTestMethod as $methodName => $method)
			{
				//echo print_r($method);
				echo " - ".str_pad($method->name, 35, '.').str_pad($methodName, 85, '.').$method->class."\n";
			}
			echo "\n";
			
			$noOutput = false;
		}
		
		if($noOutput)
		{
			echo "Es wird alles getestet ;=)";
		}
		
		echo "</pre>";
	}
	
	private function createTest($class)
	{
		$file = $this->linuxPath($class);
		$file = $file.'Test.php';
		
		$pathinfo = pathinfo($file);
		
		if($this->createDir($pathinfo['dirname']))
		{
			return $this->createFile($pathinfo, $class);
		}
		return false;
	}
	
	private function createDir($dir)
	{
		if(is_dir($dir))
		{
			return true;
		}
		
		return mkdir($dir, 0777, true);
	}
	
	private function createFile($pathinfo, $class)
	{
		$content = '<?php'."\n\n";
		$content .= 'namespace unittest\\'.str_replace('/', '\\', $pathinfo['dirname']).';'."\n\n";
		$content .= 'use \\'.$class.';'."\n\n";
		$content .= 'class '.$pathinfo['filename'].' extends \PHPUnit_Framework_TestCase'."\n";
		$content .= '{'."\n\n";
		$content .= '}'."\n";
		
		return file_put_contents($pathinfo['dirname'].'/'.$pathinfo['basename'], $content);
	}
	
	private function checkClasses()
	{		
		foreach($this->liveClasses as $namespace => $classname)
		{			
			if(!isset($this->testClasses[ $this->getUnittestClass($namespace) ]))
			{
				$this->noTestFile[$namespace] = $classname;
			}
		}
	}
	
	private function readMethods()
	{
		foreach($this->liveClasses as $namespace => $classname)
		{
			if(isset($this->noTestFile[$namespace]))
			{
				continue;
			}
			
			// Test-Methoden lesen
			$this->testMethods[$namespace] = get_class_methods( $this->getUnittestClass($namespace) );
			foreach($this->testMethods[$namespace] as $method)
			{
				$methodParts = explode('_', $method);
				if(count($methodParts) > 1)
				{
					$this->testMethodsWithUnderscore[$namespace][$method] = $methodParts[0];
				}
			}
			
			// Live-Methoden lesen
			$this->liveMethods[$namespace] = $this->getLiveMethods( $namespace );
		}
	}
	
	private function getLiveMethods($class)
	{
		return $this->getAllClassMethods($class);
		
		/*
		$file = $this->getFile($class);
		$ret = array();
		if($file)
		{
			$content = file_get_contents($file);
			$match = preg_match_all("/function (.*)/i", $content, $matches, PREG_SET_ORDER);
			
			if($match)
			{
				foreach($matches as $key => $methods)
				{
					$ret[] = $this->cleanMethodName($methods[1]);
				}
			}
		}
		
		return $ret;
		*/
	}
	
	private function getAllClassMethods($class)
	{
		$obj = new \ReflectionClass($class);
		$methods = $obj->getMethods();
		$ret = array();
		
			
		if($this->hasAbstractParent($obj))
		{
			//return $methods; Abstrakte werden genauso behandelt
		}
		
		foreach($methods as $method)
		{
			if($method->class == $class)
			{
				if ($method->isAbstract())
				{
					continue; // Abstrakte methoden werden abgeleitet getestet!
				}
				$ret[] = $method;
			}
		}
		
		return $ret;
	}
	
	private function hasAbstractParent($obj)
	{
		$parent = $obj->getParentClass();
		if($parent)
		{
			return $parent->isAbstract();
		}
		return false;
	}
	
	private function cleanMethodName($method)
	{
		$method = str_replace("\n\r", '', $method);
		$method = str_replace("\n", '', $method);
		$method = str_replace("\r", '', $method);
		$method = preg_replace("/\((.*)\)/i", '', $method);
		
		return $method;
	}
	
	private function getFile($class)
	{
		$file = $this->linuxPath($class);
		$file = str_replace($this->testFolder, '', $file);
		$file = $this->classFolder.'/'.$file.'.php';
		
		if(file_exists($file))
		{
			return $file;
		}
		return false;
	}
	
	private function checkMethods()
	{
		$this->readMethods();
		
		foreach($this->liveMethods as $namespace => $methods)
		{
			foreach($methods as $method)
			{
				if(in_array($namespace.'::'.$method->name, $this->excludeMethods))
				{
					continue;
				}
				
				if(in_array('*::'.$method->name, $this->excludeMethods))
				{
					continue;
				}
				
				if($this->allreadyShown($method))
				{
					//continue; Alles anzeigen, es gibt keine doppelten!
				}

				if(!$this->isTested($method, $namespace))
				{
					//echo $namespace.'::'.$method->name.'<br>';
					$this->noTestMethod[$namespace.'::'.$method->name] = $method;
				}
				
				$this->shownMethods[$method->class.'::'.$method->name] = $method->name;
			}
		}
	}
	
	private function allreadyShown($method)
	{
		return isset($this->shownMethods[$method->class.'::'.$method->name]);
	}
	
	private function isTested($method, $namespace)
	{
		$methodName = 'test'.ucfirst($method->name);
		if(isset($this->testMethods[$method->class]) && in_array($methodName, $this->testMethods[$method->class]))
		{
			return true;
		}
		if(isset($this->testMethods[$method->class]) && in_array($methodName, $this->testMethodsWithUnderscore[$method->class]))
		{
			return true;
		}
		
		if($method->getDeclaringClass()->isAbstract())
		{
			if(in_array($methodName, $this->testMethods[$namespace]))
			{
				return true;
			}
		}
		
		return false;
	}
	
	private function getPhpFiles($dir, $ret=array(), $prefix='')
	{
		$directory = $dir;
		$iterator = new \DirectoryIterator($directory);
		
		foreach($iterator as $iteration)
		{
			if($iteration->isDot())
			{
				continue;
			}
			
			if(in_array($this->linuxPath($iteration->getPathname()), $this->excludeFiles))
			{
				continue;
			}
			
			if($iteration->isDir())
			{
				$ret = $this->getPhpFiles($dir.'/'.$iteration->getBasename(), $ret, $prefix);
				continue;
			}
			
			if (in_array($dir,$this->ignorePath))
			{
				continue;
			}
			
			$name = $iteration->getBasename();
			$pathinfo = pathinfo($name);
			
			$namespace = $this->getNamespace($dir, $prefix);
			
			if(isset($pathinfo['extension']) && $pathinfo['extension'] == 'php' && strpos($dir,'_del_') === false && strpos($pathinfo['filename'],'__') === false )
			{
				try
				{
					$reflection = new \ReflectionClass($namespace.$pathinfo['filename']);
				}
				catch (Exception $e)
				{
					 echo 'Exception caught: ',  $e->getMessage(), "<br>";
					 continue;
				}
				if($reflection->isAbstract())
				{
					//continue; Abstrakte Klassen werden auch gecheckt!
				}
				
				$ret[$namespace.$pathinfo['filename']] = $pathinfo['filename'];
			}
		}
		
		return $ret;
	}
	
	private function linuxPath($str)
	{
		return str_replace('\\', '/', $str);
	}
	
	private function getNamespace($dir, $prefix='')
	{
		$namespace = $prefix.$dir.'/';
		
		$namespace = str_replace('../', '', $namespace);
		$namespace = str_replace('public/', '', $namespace);
		$namespace = str_replace('live/', '', $namespace);
		$namespace = str_replace('/', '\\', $namespace);
		
		return $namespace;
	}
	
	private function getUnittestClass($class)
	{
		return 'unittest\\'.$class.'Test';
	}
}