<?php

namespace jamwork\common;
use jamwork\database\Database;
use jamwork\template\Template;

class Registry
{
	// key level 1
	const KEY_SYSTEM = 'system';
	const KEY_REGISTRY = 'registry';
	// key level 2
	const KEY_REQUEST = 'request';
	const KEY_RESPONSE = 'response';
	const KEY_EVENTDISPATCHER = 'eventDispatcher';
	const KEY_DATABASE = 'database';
	const KEY_TEMPLATE = 'template';
	const KEY_SESSION = 'session';
	
	protected $values = array();
	private static $uniqueInstance = NULL;
 
    protected function __construct()
    {
    	
    }

	private final function __clone()
    {
    	
    }

	/**
	 * @static
	 * @return Registry
	 */
	public static function getInstance()
    {
        if (self::$uniqueInstance === NULL) {
            self::$uniqueInstance = new Registry();
        }
 
        return self::$uniqueInstance;
    }

	/**
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value)
	{
		$this->set($key,$value,self::KEY_REGISTRY);
	}

	/**
	 * @param $key
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this->get($key,self::KEY_REGISTRY);
	}

	/**
	 * @param $key
	 */
	public function __unset($key)
	{
		return $this->unsetKey($key,self::KEY_REGISTRY);
	}

	/**
	 * @param $key
	 * @return bool
	 */
	public function __isset($key)
	{
		return $this->hasKey($key,self::KEY_REGISTRY);
	}

	/**
	 * @param Request $request
	 */
	public function setRequest(Request $request)
	{
		$this->set(self::KEY_REQUEST, $request , self::KEY_SYSTEM);
	}

	/**
	 * @return Request
	 */
	public function getRequest()
	{
		return $this->get(self::KEY_REQUEST, self::KEY_SYSTEM );
	}

	/**
	 * @return bool
	 */
	public function hasRequest()
	{
		return $this->hasKey(self::KEY_REQUEST, self::KEY_SYSTEM );
	}

	/**
	 * @param Response $response
	 */
	public function setResponse(Response $response)
	{
		$this->set(self::KEY_RESPONSE, $response , self::KEY_SYSTEM);
	}

	/**
	 * @return Response
	 */
	public function getResponse()
	{
		return $this->get(self::KEY_RESPONSE, self::KEY_SYSTEM );
	}

	/**
	 * @return bool
	 */
	public function hasResponse()
	{
		return $this->hasKey(self::KEY_RESPONSE, self::KEY_SYSTEM );
	}

	/**
	 * @param Database $database
	 */
	public function setDatabase(Database $database)
	{
		$this->set(self::KEY_DATABASE, $database , self::KEY_SYSTEM);
	}

	/**
	 * @return Database
	 */
	public function getDatabase()
	{
		return $this->get(self::KEY_DATABASE, self::KEY_SYSTEM );
	}

	/**
	 * @return bool
	 */
	public function hasDatabase()
	{
		return $this->hasKey(self::KEY_DATABASE, self::KEY_SYSTEM );
	}

	/**
	 * @param Template $template
	 */
	public function setTemplate(Template $template)
	{
		$this->set(self::KEY_TEMPLATE, $template , self::KEY_SYSTEM);
	}

	/**
	 * @return Template
	 */
	public function getTemplate()
	{
		return $this->get(self::KEY_TEMPLATE, self::KEY_SYSTEM );
	}

	/**
	 * @return bool
	 */
	public function hasTemplate()
	{
		return $this->hasKey(self::KEY_TEMPLATE, self::KEY_SYSTEM );
	}

	/**
	 * @param Session $session
	 */
	public function setSession(Session $session)
	{
		$this->set(self::KEY_SESSION, $session , self::KEY_SYSTEM);
	}

	/**
	 * @return Session
	 */
	public function getSession()
	{
		return $this->get(self::KEY_SESSION, self::KEY_SYSTEM );
	}

	/**
	 * @return bool
	 */
	public function hasSession()
	{
		return $this->hasKey(self::KEY_SESSION, self::KEY_SYSTEM );
	}

	/**
	 * Warum ist das public? und muss es static sein?
	 * -- Vadim
	 */
	public static function reset()
	{
        self::$uniqueInstance = NULL;
    }

	/**
	 * @param EventDispatcher $eventDispatcher
	 */
	public function setEventDispatcher(EventDispatcher $eventDispatcher)
	{
		$this->set(self::KEY_EVENTDISPATCHER, $eventDispatcher , self::KEY_SYSTEM);
	}

	/**
	 * @return EventDispatcher
	 */
	public function getEventDispatcher()
	{
		return $this->get(self::KEY_EVENTDISPATCHER, self::KEY_SYSTEM );
	}

	/**
	 * @return bool
	 */
	public function hasEventDispatcher()
	{
		return $this->hasKey(self::KEY_EVENTDISPATCHER, self::KEY_SYSTEM );
	}
	
	/* Private Funktionen zum Zugriff der Interzeptoren*/

	/**
	 * @param $key
	 * @param $const
	 * @return bool
	 */
	protected function hasKey($key, $const)
	{
		if (empty($key) || empty($const))
		{
			return false;
		}
			
		return isset($this->values[$const][$key]);
	}

	/**
	 * @param $key
	 * @param $const
	 */
	protected function unsetKey($key,$const)
	{
		if($this->hasKey($key,$const)) {
			unset($this->values[$const][$key]);
		}
	}

	/**
	 * @param $key
	 * @param $value
	 * @param $const
	 */
	protected function set($key, $value , $const)
	{
		$this->values[$const][$key] = $value;
	}

	/**
	 * @param $key
	 * @param $const
	 * @return mixed
	 * @throws \Exception
	 */
	protected function get($key,$const)
	{
		if($this->hasKey($key,$const)) 
		{
			return $this->values[$const][$key];
		}
		
		throw new \Exception("Registry-Value '{$key}' ist nicht gesetzt!");
	}	

}
