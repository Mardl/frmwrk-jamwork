<?php

namespace jamwork\form;

class Trenner extends Textarea
{

	const TYPE = 'hr';

	public function getFieldType()
	{
		return self::TYPE;
	}
}