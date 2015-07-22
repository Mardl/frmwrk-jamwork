<?php

namespace jamwork\common;

/**
 * Interface Request
 *
 * @category Jamwork
 * @package  Jamwork\common
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
interface Request
{

	/**
	 * @param array $get
	 * @param array $post
	 * @param array $server
	 * @param array $cookie
	 */
	public function __construct(array $get, array $post, array $server, array $cookie);

	/**
	 * @return mixed
	 */
	public function getAllParameters();

	/**
	 * @return mixed
	 */
	public function getParameterNames();

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function hasParameter($name);

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getParameter($name);

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function issetParameter($name);

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function unsetParameter($name);

	/**
	 * @return mixed
	 */
	public function getAllCookies();

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function hasCookie($name);

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getCookie($name);

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getHeader($name);

	/**
	 * @return mixed
	 */
	public function getAllPost();

	/**
	 * @return mixed
	 */
	public function getPostNames();

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function hasPost($name);

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getPost($name);

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function issetPost($name);

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function unsetPost($name);

	/**
	 * @return mixed
	 */
	public function getRequestUri();

	/**
	 * @return mixed
	 */
	public function getScriptName();
}
