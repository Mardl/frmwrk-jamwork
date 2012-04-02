<?php

namespace jamwork\template;

class CssStylesheet implements Script
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
		$code = '';
		foreach($this->scripts as $file)
		{
			$code .= file_get_contents($file);
			$code .= "\n\n";
		}
		
		$fileName = $this->getCacheDir().md5($code).'.css';
		
		if(file_exists($fileName))
		{
			return $fileName;
		}
		
		file_put_contents($fileName, $code);
		return $fileName;
	}
	
	public function flush()
	{
		if($this->shrink)
		{
			return '<link rel="stylesheet" type="text/css" href="'.$this->getTmpFile().'" />'."\n";
		}
		return $this->getAllScripts();
	}
	
	private function getAllScripts()
	{
		$files = '';
		foreach($this->scripts as $file)
		{
			$files .= '<link rel="stylesheet" type="text/css" href="'.$file.'" />'."\n";
		}
		$files .= "\n\n";
		return $files;
	}
}
