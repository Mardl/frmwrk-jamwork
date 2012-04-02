<?php

namespace jamwork\common;

class HttpRequest implements Request
{
	private $parameters = array();
	private $server = array();
	private $cookie = array();
	private $session = array();
	private $post = array();
	# private $command = array();
	
	public function __construct(array $get, array $post, array $server, array $cookie)
	{
		$this->parameters = $this->clearArray($get);
		$this->server = $server;
		$this->cookie = $this->clearArray($cookie);
		$this->post = $this->clearArray($post);
	}
	
	private function clearArray($arr)
	{
		if (get_magic_quotes_gpc())
		{
			foreach($arr as $key => $value)
			{
				if(is_array($value))
				{
					$arr[$key] = $this->clearArray($value);
					continue;
				}
				$arr[$key] = stripslashes($value);
			}
		}
		return $arr;
	}

	/*
	public function addCommand($command)
	{
		$this->command[] = $command;
	}

	public function getCommand()
	{
		return $this->command;
	}
	*/
	
	public function getParameterNames()
	{
		return array_keys($this->parameters);
	}
	
	public function getAllParameters()
	{
		return $this->parameters;
	}
	
	public function hasParameter($name)
	{
		return $this->isKeyInArray($this->parameters, $name);
	}
	
	public function issetParameter($name)
	{
		if($this->hasParameter($name))
		{
			$var  = $this->getParameter($name);
			return !empty($var);
		}
		return false;
	}
	
	public function getParamIfExist($name, $def=null)
	{
		if($this->hasParameter($name))
		{
			return $this->getParameter($name);
		}
		return $def;
	}
	
	public function unsetParameter($name)
	{
		if($this->hasParameter($name))
		{
			unset($this->parameters[$name]);
		}
	}
	
	public function getParameter($name)
	{
		return $this->getFromKeyInArray( $this->parameters,$name);
	}

	public function setParameter($name, $value)
	{
		$this->parameters[$name] = $value;
	}


	public function getPostNames()
	{
		return array_keys($this->post);
	}
	
	public function getAllPost()
	{
		return $this->post;
	}
	
	public function hasPost($name)
	{
		return $this->isKeyInArray($this->post, $name);
	}
	
	public function issetPost($name)
	{
		if($this->hasPost($name))
		{
			$var = $this->getPost($name);
			return !empty($var);
		}
		return false;
	}
	
	public function unsetPost($name)
	{
		if($this->issetPost($name))
		{
			unset($this->post[$name]);
		}
	}
	
	public function getPost($name)
	{
		return $this->getFromKeyInArray($this->post, $name);
	}
	
	public function getPostIfExist($name, $def=null)
	{
		if($this->hasPost($name))
		{
			return $this->getPost($name);
		}
		return $def;
	}

	public function setPost($name, $value)
	{
		$this->post[$name] = $value;
	}


	public function getAllCookies()
	{
		return $this->cookie;
	}
	
	public function hasCookie($name)
	{
		return $this->isKeyInArray($this->cookie, $name);
	}
	
	public function getCookie($name)
	{
		return $this->getFromKeyInArray($this->cookie, $name);
	}
	
	public function setCookie($name, $value, $expire=0, $path='/', $domain='', $secure=false, $httponly=false)
	{
		setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
	}
	
	public function deleteCookie($name)
	{
		$this->setCookie($name, null, time()-1);
	}
	
	public function getHeader($name)
	{
		$name = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
		return $this->getFromKeyInArray($this->server, $name);
	}
	
	private function isKeyInArray($array, $name)
	{
		return isset($array[$name]) ? true : false;
	}
	
	private function getFromKeyInArray($array,$name)
	{
		if($this->isKeyInArray($array, $name))
		{
			return $array[$name];
		}
		
		throw new \Exception("Array-Parameter '{$name}' ist nicht gesetzt!");
	}
	
	public function getRequestUri()
	{
		if(isset($this->server['QUERY_STRING']))
		{
			return '?'.$this->server['QUERY_STRING'];
		}
		return '';
	}
	
	public function getServer($key)
	{
		if(isset($this->server[$key]))
		{
			return $this->server[$key];
		}
		return '';
	}
	
	public function getScriptName()
	{
		if(isset($this->server['SCRIPT_NAME']))
		{
			return '?'.$this->server['SCRIPT_NAME'];
		}
		return '';
	}
}
