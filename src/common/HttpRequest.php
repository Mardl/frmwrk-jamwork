<?php

namespace jamwork\common;

use \jamwork\common\Request;

/**
 * Class HttpRequest
 *
 * @category Jamwork
 * @package  Jamwork\common
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
class HttpRequest implements Request
{

	private $parameters = array();
	private $server = array();
	private $cookie = array();
	private $session = array();
	private $post = array();

	/**
	 * @param array $get
	 * @param array $post
	 * @param array $server
	 * @param array $cookie
	 */
	public function __construct(array $get, array $post, array $server, array $cookie)
	{
		$this->parameters = $this->clearArray($get);
		$this->server = $server;
		$this->cookie = $this->clearArray($cookie);
		$this->post = $this->clearArray($post);
	}

	/**
	 * @param array $arr
	 * @return mixed
	 */
	private function clearArray(array $arr)
	{
		if (get_magic_quotes_gpc())
		{
			foreach ($arr as $key => $value)
			{
				if (is_array($value))
				{
					$arr[$key] = $this->clearArray($value);
					continue;
				}
				$arr[$key] = stripslashes($value);
			}
		}

		return $arr;
	}

	/**
	 * @return array
	 */
	public function getParameterNames()
	{
		return array_keys($this->parameters);
	}

	/**
	 * @return array
	 */
	public function getAllParameters()
	{
		return $this->parameters;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasParameter($name)
	{
		return $this->isKeyInArray($this->parameters, $name);
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function issetParameter($name)
	{
		if ($this->hasParameter($name))
		{
			$var = $this->getParameter($name);

			return !empty($var);
		}

		return false;
	}

	/**
	 * @param string $name
	 * @param string $def
	 * @return mixed|null
	 */
	public function getParamIfExist($name, $def = null)
	{
		if ($this->hasParameter($name))
		{
			return $this->getParameter($name);
		}

		return $def;
	}

	/**
	 * @param string $name
	 * @return void
	 */
	public function unsetParameter($name)
	{
		if ($this->hasParameter($name))
		{
			unset($this->parameters[$name]);
		}
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getParameter($name)
	{
		return $this->getFromKeyInArray($this->parameters, $name);
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @return void
	 */
	public function setParameter($name, $value)
	{
		$this->parameters[$name] = $value;
	}


	/**
	 * @return array
	 */
	public function getPostNames()
	{
		return array_keys($this->post);
	}

	/**
	 * @return array
	 */
	public function getAllPost()
	{
		return $this->post;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasPost($name)
	{
		return $this->isKeyInArray($this->post, $name);
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function issetPost($name)
	{
		if ($this->hasPost($name))
		{
			$var = $this->getPost($name);

			return !empty($var);
		}

		return false;
	}

	/**
	 * @param string $name
	 * @return void
	 */
	public function unsetPost($name)
	{
		if ($this->issetPost($name))
		{
			unset($this->post[$name]);
		}
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getPost($name)
	{
		return $this->getFromKeyInArray($this->post, $name);
	}

	/**
	 * @param string $name
	 * @param string $def
	 * @return mixed|null
	 */
	public function getPostIfExist($name, $def = null)
	{
		if ($this->hasPost($name))
		{
			return $this->getPost($name);
		}

		return $def;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @return void
	 */
	public function setPost($name, $value)
	{
		$this->post[$name] = $value;
	}


	/**
	 * @return array
	 */
	public function getAllCookies()
	{
		return $this->cookie;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasCookie($name)
	{
		return $this->isKeyInArray($this->cookie, $name);
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getCookie($name)
	{
		return $this->getFromKeyInArray($this->cookie, $name);
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @param int    $expire
	 * @param string $path
	 * @param string $domain
	 * @param bool   $secure
	 * @param bool   $httponly
	 * @return void
	 */
	public function setCookie($name, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false)
	{
		setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
		$this->cookie[$name] = $value;
	}

	/**
	 * @param string $name
	 * @return void
	 */
	public function deleteCookie($name)
	{
		$this->setCookie($name, null, time() - 1);
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getHeader($name)
	{
		$name = 'HTTP_' . strtoupper(str_replace('-', '_', $name));

		return $this->getFromKeyInArray($this->server, $name);
	}


	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasHeader($name)
	{
		$name = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
		return $this->isKeyInArray($this->server, $name);
	}

	/**
	 * @param array  $array
	 * @param string $name
	 * @return bool
	 */
	private function isKeyInArray(array $array, $name)
	{
		return isset($array[$name]) ? true : false;
	}

	/**
	 * @param array  $array
	 * @param string $name
	 * @return mixed
	 * @throws \Exception
	 */
	private function getFromKeyInArray(array $array, $name)
	{
		if ($this->isKeyInArray($array, $name))
		{
			if (is_scalar($array[$name]))
			{
				return trim($array[$name]);
			}
			array_walk_recursive($array[$name], 'trim');

			return $array[$name];
		}

		throw new \Exception("Array-Parameter '{$name}' ist nicht gesetzt!");
	}

	/**
	 * @return string
	 */
	public function getRequestUri()
	{
		if (isset($this->server['QUERY_STRING']))
		{
			return '?' . $this->server['QUERY_STRING'];
		}

		return '';
	}

	/**
	 * @param string $key
	 * @param string $default
	 * @return string
	 */
	public function getServer($key, $default = '')
	{
		if (isset($this->server[$key]))
		{
			return $this->server[$key];
		}

		return $default;
	}

	/**
	 * @return string
	 */
	public function getScriptName()
	{
		if (isset($this->server['SCRIPT_NAME']))
		{
			return '?' . $this->server['SCRIPT_NAME'];
		}

		return '';
	}
}
