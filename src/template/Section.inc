<?php

namespace jamwork\template;

/**
 * Class Section
 *
 * @category Jamwork
 * @package  Jamwork\template
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
interface Section
{

	/**
	 * @param string $data
	 * @return mixed
	 */
	public function append($data);

	/**
	 * @param string $data
	 * @return mixed
	 */
	public function prepend($data);

	/**
	 * @return mixed
	 */
	public function flush();
}
