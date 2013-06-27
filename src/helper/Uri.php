<?php

namespace jamwork\helper;

use \jamwork\common\Request;

class Uri
{

	private $request = null;
	private $param = array();

	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->param = $this->request->getAllParameters();
	}

	public function set($name, $value)
	{
		$this->param[$name] = $value;
	}

	public function remove($name)
	{
		if ($this->is($name))
		{
			unset($this->param[$name]);
		}
	}

	public function is($name)
	{
		return isset($this->param[$name]);
	}

	public function __toString()
	{
		$str = array();
		foreach ($this->param as $key => $value)
		{
			$str[] = $key . '=' . $value;
		}

		return '?' . implode('&', $str);
	}
}