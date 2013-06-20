<?php

namespace jamwork\debug;

require_once __DIR__.'/FirePHPCore/FirePHP.class.php';

class DebugLogger
{
	private static $uniqueInstance = NULL;
	private $fb = NULL;
	private $active = false;
	private $showTodo = false;
	private $showInfo = false;
 
    protected function __construct()
    {
    	$this->fb = new \FirePHP();
    }
	
    private final function __clone()
    {
    	
    }
 
    public static function getInstance()
    {
        if (self::$uniqueInstance === NULL) {
            self::$uniqueInstance = new DebugLogger();
        }
 
        return self::$uniqueInstance;
    }
	
	public function setActive($bool)
	{
		$this->active = $bool;
	}
	
	public function setShowTodo($bool)
	{
		$this->showTodo = $bool;
	}
	
	public function setShowInfo($bool)
	{
		$this->showInfo = $bool;
	}
	
	private function isActive()
	{
		return $this->active;
	}
	
	private function isShowTodo()
	{
		return $this->showTodo;
	}
	
	private function isShowInfo()
	{
		return $this->showInfo;
	}
	
	public function ram()
	{
		$ram = memory_get_usage() / 1024 / 1024;
		$peak = memory_get_peak_usage() / 1024 / 1024;

		$this->info(number_format($ram, 3, ',', '.').' MB', str_pad('RAM', 20, '-') );
		$this->info(number_format($peak, 3, ',', '.').' MB', str_pad('RAM-Peak', 20, '-'));
	}
	
	public function log($Object, $Label = null, $Options = array())
	{
		if(!$this->isActive()) 
		{
			return;
		}
		
		$this->fb->log($Object, $Label, $Options);
	}
	
	public function todo($Object, $line, $file)
	{
		if(!$this->isShowTodo()) 
		{
			return;
		}
		
		$this->fb->info($Object, 'TODO in '.$file. ' on line '.$line);
	}

	public function info($Object, $Label = null, $Options = array())
	{
		if(!$this->isShowInfo()) 
		{
			return;
		}
		
		$this->fb->info($Object, $Label, $Options);
	}
	
	public function warn($Object, $Label = null, $Options = array())
	{
		if(!$this->isActive()) 
		{
			return;
		}
		
		$this->fb->warn($Object, $Label, $Options);
	}
	
	public function error($Object, $Label = null, $Options = array())
	{
		if(!$this->isActive()) 
		{
			return;
		}
		
		$this->fb->error($Object, $Label, $Options);
	}
	
	public function dump($Key, $Variable, $Options = array())
	{
		if(!$this->isActive()) 
		{
			return;
		}
		
		$this->fb->dump($Key, $Variable, $Options);
	}
	
	public function trace($Label)
	{
		if(!$this->isActive()) 
		{
			return;
		}
		
		$this->fb->trace($Label);
	}
	
	public function table($Label, $Table, $Options = array())
	{
		if(!$this->isActive()) 
		{
			return;
		}
		
		$this->fb->table($Label, $Table, $Options);
	}
}