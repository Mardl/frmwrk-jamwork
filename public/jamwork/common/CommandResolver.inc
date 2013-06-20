<?php

namespace jamwork\common;

interface CommandResolver
{
	public function getCommand(Request $request);
}