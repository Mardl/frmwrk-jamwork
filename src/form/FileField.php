<?php

namespace jamwork\form;

class Filefield extends Textfield
{

	const TYPE = 'file';

	private $maxLength = '';

	public function getFieldType()
	{
		return self::TYPE;
	}

}