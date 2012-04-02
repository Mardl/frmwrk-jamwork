<?php

namespace jamwork\form;

class Button extends AbstractField
{
	const TYPE = 'button';
	
	protected $buttonType = 'submit';
	protected $text = '';
	
	public function getFieldType()
	{
		return self::TYPE;	
	}
	
	public function buttonType($type)
	{
		$this->buttonType = $type;
		return $this;
	} 
	
	public function getButtonType()
	{
		return $this->buttonType;
	}
	
	public function text($text)
	{
		$this->text = $text;
		return $this;
	} 
	
	public function getText()
	{
		return $this->text;
	}

}