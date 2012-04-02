<?php

namespace jamwork\helper;

use \jamwork\common\Registry;
use \jamwork\helper\UrlGenerator;
use \jamwork\helper\DefaultUrlGenerator;

class Hyperlink
{
	private $cmd = '';
	private $param = array();
	private $urlGenerator = null;
	
	public function command($cmd)
	{
		$this->cmd = $cmd;
		return $this;
	}
	
	public function setParam($key, $value)
	{
		$this->param[$key] = $value;
		return $this;
	}
	
	public function setUrlGenerator(UrlGenerator $urlGenerator)
	{
		$this->urlGenerator = $urlGenerator;
	}
	
	public function unsetParam($key)
	{
		unset($this->param[$key]);
		return $this;
	}
	
	private function getUrlGenerator()
	{
		$reg = Registry::getInstance();
		$eventsDispatcher = $reg->getEventDispatcher();
		$eventsDispatcher->triggerEvent('onGenerateUrlString', $this);
		
		if($this->urlGenerator === null)
		{
			$this->urlGenerator = new DefaultUrlGenerator();
		}
		return $this->urlGenerator;
	}
	
	public function getUrl()
	{
		$generator = $this->getUrlGenerator();
		return $generator->generate($this->cmd, $this->param);
		// '?cmd='.$this->cmd . $this->getParamString();
	}
	
	public function getCurrent()
	{
		$reg = Registry::getInstance();
		$request = $reg->getRequest();
		
		foreach($request->getAllParameters() as $key => $value)
		{
			if($key == 'cmd')
			{
				$this->command($value);
				continue;
			}
			$this->setParam($key, $value);
		}
		return $this;
	}
}