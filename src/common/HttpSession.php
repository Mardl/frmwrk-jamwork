<?php

namespace jamwork\common;

/**
 * Class HttpSession
 *
 * @category Jamwork
 * @package  Jamwork\common
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
class HttpSession implements Session
{

	private $session = array();

	/**
	 * @param string $SID
	 */
	public function __construct($SID = '')
	{
		if (!empty($SID))
		{
			session_id($SID);
		}
		session_start();
		$this->session = $_SESSION;
	}

	/**
	 * destructor
	 */
	public function __destruct()
	{
		$_SESSION = $this->session;
	}

	/**
	 * @return mixed|string
	 */
	public function getId()
	{
		return session_id();
	}

	/**
	 * @param string $name
	 * @return mixed|string
	 */
	public function get($name)
	{
		if ($this->has($name))
		{
			return $this->session[$name];
		}

		return '';
	}

	/**
	 * @param string $name
	 * @return mixed|void
	 */
	public function delete($name)
	{
		if ($this->has($name))
		{
			unset($this->session[$name]);
		}
	}

	/**
	 * @param string $name
	 * @return bool|mixed
	 */
	public function has($name)
	{
		return isset($this->session[$name]);
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @return mixed|void
	 */
	public function set($name, $value)
	{
		$this->session[$name] = $value;
	}

	/**
	 * @return bool|mixed
	 */
	public function destroy()
	{
		return session_destroy();
	}
}