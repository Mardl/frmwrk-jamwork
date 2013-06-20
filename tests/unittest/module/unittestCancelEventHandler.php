<?php

namespace unittest\module;

use \jamwork\common\EventHandler;

class UnittestCancelEventHandler implements EventHandler
{
	public function handle($event)
	{
		$event->cancel();
		return false;
	}
}

