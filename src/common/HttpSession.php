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

	public function __construct($SID = '')
	{
		if (!empty($SID))
		{
			session_id($SID);
		}
		session_start();
		$this->session = $_SESSION;
	}

	public function __destruct()
	{
		$_SESSION = $this->session;
	}

	public function getId()
	{
		return session_id();
	}

	public function get($name)
	{
		if ($this->has($name))
		{
			return $this->session[$name];
		}

		return '';
	}

	public function delete($name)
	{
		if ($this->has($name))
		{
			unset($this->session[$name]);
		}
	}

	public function has($name)
	{
		return isset($this->session[$name]);
	}

	public function set($name, $value)
	{
		$this->session[$name] = $value;
	}

	public function destroy()
	{
		return session_destroy();
	}
}