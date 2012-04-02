<?php

namespace jamwork\form;

class Select extends AbstractField
{
	const TYPE = 'select';
	
	private $multiple = false;
		
	public function getFieldType()
	{
		return self::TYPE;	
	}
	
	public function newOption()
	{
		$option = new Option();
		$this->value[] = $option;
		
		return $option;
	}
		
	public function multiple()
	{
		$this->multiple = true;
		return $this;
	}
	
	public function isMultiple()
	{
		return $this->multiple;
	}
}