<?php

namespace jamwork\common;

class WebServiceHandler
{

	private $strServiceClass = '';
	private $strWsdlPath = '';
	private $bPersistence = '';


	public function __construct($strServiceClass, $strWsdlPath, $bPersistence)
	{
		$this->strServiceClass = $strServiceClass;
		$this->strWsdlPath = $strWsdlPath;
		$this->bPersistence = $bPersistence;
	}


	public function getServiceClass()
	{
		return $this->strServiceClass;
	}

	public function getWsdlPath()
	{
		return $this->strServiceClass;
	}

	public function isPersistence()
	{
		return $this->bPersistence;
	}

	public function createServices()
	{
		$server = new \SoapServer($this->strWsdlPath);
		$server->setClass($this->strServiceClass);
		$server->setPersistence($this->bPersistence);
		$server->handle();
	}

}
