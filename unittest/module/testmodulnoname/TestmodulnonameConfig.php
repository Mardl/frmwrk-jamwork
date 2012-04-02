<?php

namespace unittest\module\testmodulnoname;

use \jamwork\modulverwaltung\AbstractModulConfig;

class TestmodulnonameConfig extends AbstractModulConfig
{
	const VERSION = '1.0.0';
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
