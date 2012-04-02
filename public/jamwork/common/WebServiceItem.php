<?php

namespace jamwork\common;

class WebServiceItem
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
}
