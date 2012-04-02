<?php

namespace unittest\module\testmodul1;

use \jamwork\modulverwaltung\AbstractModulConfig;

class Testmodul1Config extends AbstractModulConfig
{
	const VERSION = '1.0.0';
	const NAME = 'Testmodul1';
	const SHOW = 1;
	const SORT = 1;
	
	public function install()
	{
		return true;
	}
	
	public function update($dbVersion)
	{
		return $this->getVersion();
	}
}
