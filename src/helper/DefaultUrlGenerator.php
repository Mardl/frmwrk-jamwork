<?php

namespace jamwork\helper;

use \jamwork\common\Registry;

class DefaultUrlGenerator implements UrlGenerator
{

	public function generate($cmd, $params)
	{
		return '?cmd=' . $cmd . $this->getParamString($params);
	}

	private function getParamString($params)
	{
		$str = '';
		foreach ($params as $key => $value)
		{
			$str .= '&' . $key . '=' . $value;
		}

		return $str;
	}
}