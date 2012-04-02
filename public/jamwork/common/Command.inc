<?php

namespace jamwork\common;

interface Command
{
	public function execute(Request $request, Response $response);
}
