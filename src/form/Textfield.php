<?php

namespace jamwork\form;

class Textfield extends Textarea
{
	const TYPE = 'text';
	
	private $maxLength = '';
	
	public function getFieldType()
	{
		return self::TYPE;	
	}
	
	public function maxLength($length)
	{
		$this->maxLength = $length;
		return $this;
	}
	
	public function getMaxLength()
	{
		return $this->maxLength;
	}	
}