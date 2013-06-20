<?php

namespace jamwork\template;

class HtmlTemplate implements Template
{
	protected $templateDir = '';
	protected $templateFile = '';
	protected $templateFiles = array();
	protected $js = null;
	protected $css = null;
	protected $sections = array();
	protected $mainSection = '';
	protected $body = '';
	protected $sectionList = array();
	protected $doctype = '';
	protected $xmlHeader = '';
	protected $baseUrl = '';
	protected $title = '';
	protected $minify = true;
	protected $meta = array();
	
	public function __construct($templateDir, $shrink=true)
	{
		$this->js = new Javascript($shrink);
		$this->css = new CssStylesheet($shrink);

		$this->setTemplateDir($templateDir);

		$this->addMeta('language', 'de');
		$this->addMeta('Content-Type', 'text/html; charset=utf-8', 'http-equiv');
	}
	
	public function __destruct()
	{
		unset($this->js);
		unset($this->css);
		
		foreach($this->sections as $obj)
		{
			unset($obj);
		}
	}
	
	public function addMeta($key, $value, $keyName='name')
	{
		$this->meta[$keyName.'="'.$key.'"'] = $value;
	}
	
	public function getMeta()
	{
		$strOut = array();
		foreach($this->meta as $key => $content)
		{
			$strOut[] = '<meta '.$key.' content="'.$content.'" />';
		}
		
		return implode("\n", $strOut);
	}
	
	public function setCacheDir($dir)
	{
		if(!is_dir($dir))
		{
			mkdir($dir, 0777);
			chmod($dir, 0777);
		}
		
		if(substr($dir, -1, 1) != '/')
		{
			$dir .= '/';
		}
		
		$this->css()->setCacheDir($dir);
		$this->js()->setCacheDir($dir);
	}
	
	public function setTemplateFile($templateFile)
	{
		if(in_array($templateFile, $this->templateFiles))
		{
			$this->templateFile = $templateFile;
			$this->readTemplate();
			return true;
		}
		return false;
	}

	public function js()
	{
		return $this->js;
	}
	
	public function css()
	{
		return $this->css;
	}
	
	public function section($key)
	{
		if(isset($this->sections[$key]))
		{
			return $this->sections[$key];
		}
		
		throw new \Exception('Die geforderte Section mit dem Key "'.$key.'" existiert nicht!');
	}
	
	public function setMainSection($sectionName)
	{
		if(isset($this->sections[$sectionName]))
		{
			$this->mainSection = $sectionName;
			return;
		}
		
		throw new \Exception('Die Section "'.$sectionName.'" existiert nicht und kann nicht als Main-Section gesetzt werden.');
	}
	
	public function mainSection()
	{
		if(!empty($this->mainSection))
		{
			return $this->section($this->mainSection);
		}
		
		throw new \Exception('Die Main-Section wurde nicht definiert!');
	}

	public function flush()
	{
		$strOut = $this->getXmlHeader();
		$strOut .= $this->getDoctype();
		$strOut .= '<head>'."\n";
		$strOut .= $this->getBaseUrl();
		$strOut .= $this->getMeta()."\n"."\n";
		$strOut .= $this->getTitle();
		$strOut .= $this->getFavicon();
		$strOut .= $this->css()->flush();
		$strOut .= $this->js()->flush();
		$strOut .= '</head>'."\n";
		$strOut .= $this->getBody();
		$strOut .= '</html>';
		return $strOut;
	}
	
	private function getFavicon()
	{
		$icon = '';

		$file = $this->templateDir.'favicon.ico';
		if(empty($icon) && is_file($file))
		{
			$icon = '<link rel="shortcut icon" href="'.$file.'" type="image/x-icon" />'."\n";
		}

		$file = 'favicon.ico';
		if(empty($icon) && is_file($file))
		{
			$icon = '<link rel="shortcut icon" href="'.$file.'" type="image/x-icon" />'."\n";
		}

		return $icon;
	}
	
	public function getSectionList()
	{
		return $this->sectionList;
	}
	
	public function setDoctype($doctype)
	{
		$this->doctype = $doctype;
	}

	public function setXmlHeader($xmlHeader)
	{
		$this->xmlHeader = $xmlHeader;
	}
	
	public function setBaseUrl($baseUrl)
	{
		$this->baseUrl = $baseUrl;
	}	

	private function readFiles()
	{
		$iterator = new \DirectoryIterator($this->templateDir);
		foreach($iterator as $iteration)
		{
			if($iteration->isDot())
			{
				continue;
			}
			
			if($iteration->isFile() && substr($iteration->getFilename(), -5) == '.html')
			{
				$this->templateFiles[$iteration->getFilename()] = $iteration->getBasename('.html');
			}
			
			if($iteration->isDir())
			{
				$this->readSubDir($this->templateDir.$iteration->getBasename().'/');
			}
		}
		unset($iterator);
	}
	
	private function readSubDir($dir)
	{
		$iterator = new \DirectoryIterator($dir);
		foreach($iterator as $iteration)
		{
			if($iteration->isFile())
			{
				$pathinfo = pathinfo($iteration->getBasename());
				switch($pathinfo['extension'])
				{
					case 'css':
						$this->css()->add($dir.$iteration->getBasename());
						break;
					case 'js':
						$this->js()->add($dir.$iteration->getBasename());
						break;
				}
			}
		}
		unset($iterator);
		$this->css()->ksort();
		$this->js()->ksort();
	}
	
	private function readTemplate()
	{
		$tplFile = $this->templateDir.$this->templateFile.'.html';
		$this->body = file_get_contents($tplFile);
		$isMatch = preg_match_all('/\{\$(.*?)\}/i', $this->body, $matches);
		if ($isMatch) 
		{
			foreach ($matches[0] as $matchkey => $matchwert)
			{
				$section = new HtmlSection();
				$this->sections[$matches[1][$matchkey]] = $section;
				$this->sectionList[] = $matches[1][$matchkey];
			}
		}
	}
	
	private function setTemplateDir($template)
	{
		if(substr($template, -1) != '/')
		{
			$template .= '/';
		}
		
		if(is_dir($template))
		{
			$this->templateDir = $template;
			$this->readFiles();
			return;
		}
		
		throw new \Exception('Template-Datei ('.$template.') konnte nicht gefunden werden.');
	}
		
	private function getBody()
	{
		foreach($this->sections as $key => $obj)
		{
			$this->body = str_replace('{$'.$key.'}', $obj->flush(), $this->body);
		}
		return $this->body."\n";
	}
	
	private function getDoctype()
	{
		if(empty($this->doctype))
		{
			$doc = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"'."\n";
       		$doc .= "\t".'"http://www.w3.org/TR/html4/loose.dtd">'."\n";
			$doc .= '<html>'."\n";
			return $doc;
		}
		return $this->doctype."\n";
	}

	private function getXmlHeader()
	{
		if(empty($this->xmlHeader))
		{
			return '';
		}
		return $this->xmlHeader."\n";
	}
	
	private function getTitle()
	{
		return '<title>'.$this->title.'</title>'."\n";
	}
	
	private function getBaseUrl()
	{
		if(empty($this->baseUrl))
		{
			return '';
		}
		return '<base href="'.$this->baseUrl.'" />'."\n\n";
	}	
}
