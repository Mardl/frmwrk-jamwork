<?php

namespace jamwork\common;

class HttpSession implements Session
{
	private $session = array();
	
	public function __construct()
	{
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
		if($this->has($name))
		{
			return $this->session[$name];
		}
	}
	
	public function delete($name)
	{
		if($this->has($name))
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