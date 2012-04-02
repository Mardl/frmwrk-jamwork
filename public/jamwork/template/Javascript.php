<?php

namespace jamwork\template;

class Javascript implements Script
{
	private $scripts = array();
	private $cacheDir = 'static/';
	private $shrink = false;
	
	public function __construct($shrink = false)
	{
		$this->shrink = $shrink;
	}
	
	public function add($file)
	{
		if(file_exists($file))
		{
			$this->scripts[$file] = $file;
			return true;
		}
		return false;
	}
	
	public function setCacheDir($dir)
	{
		$this->cacheDir = $dir;
	}
	
	public function ksort()
	{
		ksort($this->scripts);
	}
	
	private function getCacheDir()
	{
		return $this->cacheDir;
	}

	public function remove($file)
	{
		if(isset($this->scripts[$file]))
		{
			unset($this->scripts[$file]);
			return true;
		}
		return false;
	}
	
	private function getTmpFile()
	{
		// hat Optimierungsbedarf, siehe EGARA
		$jsCode = '';
		foreach($this->scripts as $file)
		{
			$jsCode .= file_get_contents($file);
			$jsCode .= "\n\n";
		}
		$fileName = $this->getCacheDir().md5($jsCode).'.js';
		
		if(file_exists($fileName))
		{
			return $fileName;
		}
		
		file_put_contents($fileName, $jsCode);
		return $fileName;
	}
	
	public function flush()
	{
		if($this->shrink)
		{
			return '<script type="text/javascript" src="'.$this->getTmpFile().'"></script>'."\n";
		}
		return $this->getAllScripts();
	}
	
	private function getAllScripts()
	{
		$files = '';
		foreach($this->scripts as $file)
		{
			$files .= '<script type="text/javascript" src="'.$file.'"></script>'."\n";
		}
		$files .= "\n\n";
		return $files;
	}
}
