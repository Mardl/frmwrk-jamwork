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
	private $sid = null;

	/**
	 * @param string $SID
	 */
	public function __construct($SID = '', $startSession=true)
	{
		$this->sid = $SID;
		if ($startSession)
		{
			$this->startSession();
		}
	}

	public function startSession()
	{
		if (!empty($this->sid))
		{
			session_id($this->sid);
		}
		session_start();
		$this->session = array_merge_recursive($_SESSION, $this->session);
	}

	/**
	 * destructor
	 */
	public function __destruct()
	{
		if ($this->isSessionStarted())
		{
			$_SESSION = $this->session;
		}
	}

	private function isSessionStarted()
	{
		return isset($_SESSION);
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
		if ($this->isSessionStarted())
		{
			return session_destroy();
		}
		return true;
	}
}