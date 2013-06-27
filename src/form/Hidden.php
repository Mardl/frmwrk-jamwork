<?php

namespace jamwork\form;

class Hidden extends Textfield
{

	const TYPE = 'hidden';

	public function getFieldType()
	{
		return self::TYPE;
	}
}