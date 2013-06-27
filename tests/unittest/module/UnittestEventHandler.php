<?php

namespace unittest\module;

use \jamwork\common\EventHandler;

class UnittestEventHandler implements EventHandler
{
	public function handle($event)
	{
		return true;
	}
}

