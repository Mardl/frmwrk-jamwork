<?php

namespace jamwork\form;

class Ptag extends Textarea
{
	const TYPE = 'p';
	
	public function getFieldType()
	{
		return self::TYPE;	
	}
}