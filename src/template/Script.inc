<?php

namespace jamwork\template;

/**
 * Class Script
 *
 * @category Jamwork
 * @package  Jamwork\template
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
interface Script
{

	/**
	 * @param string $file
	 * @return mixed
	 */
	public function add($file);

	/**
	 * @param string $file
	 * @return mixed
	 */
	public function remove($file);

	/**
	 * @return mixed
	 */
	public function flush();

	/**
	 * @param string $dir
	 * @return mixed
	 */
	public function setCacheDir($dir);
}
