<?php

namespace jamwork\common;

/**
 * Interface Session
 *
 * @category Jamwork
 * @package  Jamwork\common
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
interface Session
{

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function get($name);

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function has($name);

	/**
	 * @param string $name
	 * @param string $value
	 * @return mixed
	 */
	public function set($name, $value);

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function delete($name);

	/**
	 * @return mixed
	 */
	public function destroy();

	/**
	 * @return mixed
	 */
	public function getId();
}