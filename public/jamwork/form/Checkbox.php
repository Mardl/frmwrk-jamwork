<?php

namespace jamwork\form;

class Checkbox extends Textarea
{
	protected $checked = false;
	
	const TYPE = 'checkbox';
	
	public function getFieldType()
	{
		return self::TYPE;	
	}
	
	public function checked()
	{
		$this->checked = true;
	}
	
	public function isChecked()
	{
		return $this->checked;
	}
}