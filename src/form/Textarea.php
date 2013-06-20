<?php

namespace jamwork\form;

class Textarea extends AbstractField
{
	const TYPE = 'textarea';
	
	protected $readOnly = false;
	
	public function getFieldType()
	{
		return self::TYPE;	
	}
	
	public function readOnly()
	{
		$this->readOnly = true;
		return $this;
	}
	
	public function isReadOnly()
	{
		return $this->readOnly;
	}
}