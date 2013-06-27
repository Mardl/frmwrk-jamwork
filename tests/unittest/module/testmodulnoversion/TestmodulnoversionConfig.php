<?php

namespace unittest\module\testmodulnoversion;

use \jamwork\modulverwaltung\AbstractModulConfig;

class TestmodulnoversionConfig extends AbstractModulConfig
{

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
