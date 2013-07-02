<?php

namespace jamwork\template;

/**
 * Class HtmlSection
 *
 * @category Jamwork
 * @package  Jamwork\template
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
class HtmlSection implements Section
{

	private $data = '';

	/**
	 * @param string $data
	 * @return void
	 */
	public function append($data)
	{
		$this->data .= $data;
	}

	/**
	 * @param string $data
	 * @return void
	 */
	public function prepend($data)
	{
		$this->data = $data . $this->data;
	}

	/**
	 * @return string
	 */
	public function flush()
	{
		return $this->data;
	}
}
