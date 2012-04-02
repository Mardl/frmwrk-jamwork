<?php

namespace jamwork\form;

class Password extends Textfield
{
	const TYPE = 'password';
	
	public function getFieldType()
	{
		return self::TYPE;	
	}
}