<?php

namespace jamwork\template;

class HtmlSection implements Section
{

	private $data = '';

	public function append($data)
	{
		$this->data .= $data;
	}

	public function prepend($data)
	{
		$this->data = $data . $this->data;
	}

	public function flush()
	{
		return $this->data;
	}
}
