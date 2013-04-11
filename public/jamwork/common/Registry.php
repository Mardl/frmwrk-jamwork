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

	/**
	 * @var $uniqueInstance Registry
	 */
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

	public function __set($key, $value)
	{
		$this->set($key,$value,self::KEY_REGISTRY);
	}

	public function __get($key)
	{
		return $this->get($key,self::KEY_REGISTRY);
	}

	public function __unset($key)
	{
		return $this->unsetKey($key,self::KEY_REGISTRY);
	}
	
	public function __isset($key)
	{
		return $this->hasKey($key,self::KEY_REGISTRY);
	}

	public function setRequest(Request $request)
	{
		$this->set(self::KEY_REQUEST, $request , self::KEY_SYSTEM);
	}
	
	public function getRequest()
	{
		return $this->get(self::KEY_REQUEST, self::KEY_SYSTEM );
	}
	
	public function hasRequest()
	{
		return $this->hasKey(self::KEY_REQUEST, self::KEY_SYSTEM );
	}

	
	public function setResponse(Response $response)
	{
		$this->set(self::KEY_RESPONSE, $response , self::KEY_SYSTEM);
	}
	
	public function getResponse()
	{
		return $this->get(self::KEY_RESPONSE, self::KEY_SYSTEM );
	}
	
	public function hasResponse()
	{
		return $this->hasKey(self::KEY_RESPONSE, self::KEY_SYSTEM );
	}

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
	
	public function hasDatabase()
	{
		return $this->hasKey(self::KEY_DATABASE, self::KEY_SYSTEM );
	}

	public function setTemplate(Template $template)
	{
		$this->set(self::KEY_TEMPLATE, $template , self::KEY_SYSTEM);
	}
	
	public function getTemplate()
	{
		return $this->get(self::KEY_TEMPLATE, self::KEY_SYSTEM );
	}
	
	public function hasTemplate()
	{
		return $this->hasKey(self::KEY_TEMPLATE, self::KEY_SYSTEM );
	}

	public function setSession(Session $session)
	{
		$this->set(self::KEY_SESSION, $session , self::KEY_SYSTEM);
	}
	
	public function getSession()
	{
		return $this->get(self::KEY_SESSION, self::KEY_SYSTEM );
	}
	
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
	
	public function setEventDispatcher(EventDispatcher $eventDispatcher)
	{
		$this->set(self::KEY_EVENTDISPATCHER, $eventDispatcher , self::KEY_SYSTEM);
	}
	
	public function getEventDispatcher()
	{
		return $this->get(self::KEY_EVENTDISPATCHER, self::KEY_SYSTEM );
	}
	
	public function hasEventDispatcher()
	{
		return $this->hasKey(self::KEY_EVENTDISPATCHER, self::KEY_SYSTEM );
	}
	
	/* Private Funktionen zum Zugriff der Interzeptoren*/
	 
	private function hasKey($key, $const)
	{
		if (empty($key) || empty($const))
		{
			return false;
		}
			
		return isset($this->values[$const][$key]);
	}

	private function unsetKey($key,$const)
	{
		if($this->hasKey($key,$const)) {
			unset($this->values[$const][$key]);
		}
	}

	private function set($key, $value , $const)
	{
		$this->values[$const][$key] = $value;
	}
	
	private function get($key,$const)
	{
		if($this->hasKey($key,$const)) 
		{
			return $this->values[$const][$key];
		}
		
		throw new \Exception("Registry-Value '{$key}' ist nicht gesetzt!");
	}	

}
