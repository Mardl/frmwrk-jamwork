<?php

namespace jamwork\modulverwaltung;

abstract class AbstractModulConfig
{
	const VERSION = false;
	const NAME = false;
	const SHOW = 1;
	const SORT = false;
	
	abstract public function install();
	abstract public function update($dbVersion);
	
	public function getVersion()
	{
		return $this::VERSION;
	}
	
	public function getName()
	{
		return $this::NAME;
	}
	
	public function getShow()
	{
		return $this::SHOW;
	}
	
	public function getSort()
	{
		return $this::SORT;
	}
	
	public function isVersionLower($dbVersion, $modulVersion)
	{
		$dbVersion = explode('.', $dbVersion);
		$modulVersion = explode('.', $modulVersion);
		
		if($dbVersion[0] < $modulVersion[0])
		{
			return true;
		}
		if($dbVersion[0] == $modulVersion[0] && $dbVersion[1] < $modulVersion[1])
		{
			return true;
		}
		if($dbVersion[0] == $modulVersion[0] && $dbVersion[1] == $modulVersion[1] && $dbVersion[2] < $modulVersion[2])
		{
			return true;
		}
		
		return false;
	}
}