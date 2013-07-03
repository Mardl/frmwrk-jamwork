<?php

namespace jamwork\common;

/**
 * Interface Response
 *
 * @category Jamwork
 * @package  Jamwork\common
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
interface Response
{

	/**
	 * @param string $status
	 * @return mixed
	 */
	public function setStatus($status);

	/**
	 * @param string $name
	 * @param string $value
	 * @return mixed
	 */
	public function addHeader($name, $value);

	/**
	 * @param string $data
	 * @return mixed
	 */
	public function write($data);

	/**
	 * @param bool $dump
	 * @return mixed
	 */
	public function flush($dump = false);

	/**
	 * @return mixed
	 */
	public function getBody();

	/**
	 * @param string $body
	 * @return mixed
	 */
	public function setBody($body);

	/**
	 * @return mixed
	 */
	public function getHeader();

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function unsetHeader($name);

	/**
	 * @return mixed
	 */
	public function getStatus();

	/**
	 * @param Command $obj
	 * @param mixed   $data
	 * @return mixed
	 */
	public function addReturn(Command $obj, $data);
}
