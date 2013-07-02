<?php

namespace jamwork\form;

class Radiobutton extends Checkbox
{

	const TYPE = 'radio';

	public function getFieldType()
	{
		return self::TYPE;
	}
}