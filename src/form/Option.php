<?php

namespace jamwork\form;

class Option extends AbstractField
{

	const TYPE = 'option';

	private $selected = false;
	private $text = '';
	protected $value = '';

	public function getFieldType()
	{
		return self::TYPE;
	}

	public function text($text)
	{
		$this->text = $text;

		return $this;
	}

	public function value($value)
	{
		$this->value = $value;

		return $this;
	}

	public function getText()
	{
		return $this->text;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function selected($bool = true)
	{
		$this->selected = $bool;

		return $this;
	}

	public function isSelected()
	{
		return $this->selected;
	}
}