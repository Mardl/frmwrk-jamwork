<?php

namespace unittest\module\testmodul1;

use jamwork\common\Registry;
use jamwork\common\Request;
use jamwork\common\Response;

class Testmodul1Command implements \jamwork\common\Command
{

	const NAME = 'Testseite1';
	const SORT = 1;
	const SHOW = 1;

	public function __construct()
	{
		$registry = Registry::getInstance();
	}

	public function execute(Request $request, Response $response)
	{
		$response->addReturn($this, 'Bitte eine CMD eingeben ;)');
	}
}