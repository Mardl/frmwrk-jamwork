<?php

namespace jamwork\template;

/**
 * Class CssStylesheet
 *
 * @category Jamwork
 * @package  Jamwork\template
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
class CssStylesheet implements Script
{

	private $scripts = array();
	private $cacheDir = 'static/';
	private $shrink = false;

	/**
	 * @param bool $shrink
	 */
	public function __construct($shrink = false)
	{
		$this->shrink = $shrink;
	}

	/**
	 * @param string $file
	 * @return bool
	 */
	public function add($file)
	{
		if (file_exists($file))
		{
			$this->scripts[$file] = $file;

			return true;
		}

		return false;
	}

	/**
	 * @param string $dir
	 * @return void
	 */
	public function setCacheDir($dir)
	{
		$this->cacheDir = $dir;
	}

	/**
	 * @return void
	 */
	public function ksort()
	{
		ksort($this->scripts);
	}

	/**
	 * @return string
	 */
	private function getCacheDir()
	{
		return $this->cacheDir;
	}

	/**
	 * @param string $file
	 * @return bool
	 */
	public function remove($file)
	{
		if (isset($this->scripts[$file]))
		{
			unset($this->scripts[$file]);

			return true;
		}

		return false;
	}

	/**
	 * @return string
	 */
	private function getTmpFile()
	{
		// hat Optimierungsbedarf, siehe EGARA
		$code = '';
		foreach ($this->scripts as $file)
		{
			$code .= file_get_contents($file);
			$code .= "\n\n";
		}

		$fileName = $this->getCacheDir() . md5($code) . '.css';

		if (file_exists($fileName))
		{
			return $fileName;
		}

		file_put_contents($fileName, $code);

		return $fileName;
	}

	/**
	 * @return string
	 */
	public function flush()
	{
		if ($this->shrink)
		{
			return '<link rel="stylesheet" type="text/css" href="' . $this->getTmpFile() . '" />' . "\n";
		}

		return $this->getAllScripts();
	}

	/**
	 * @return string
	 */
	private function getAllScripts()
	{
		$files = '';
		foreach ($this->scripts as $file)
		{
			$files .= '<link rel="stylesheet" type="text/css" href="' . $file . '" />' . "\n";
		}
		$files .= "\n\n";

		return $files;
	}
}
