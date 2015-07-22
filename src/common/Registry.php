<?php

namespace jamwork\common;

use jamwork\database\Database;
use jamwork\template\Template;

/**
 * Class Registry
 *
 * @category Jamwork
 * @package  Jamwork\common
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
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
	private static $uniqueInstance = null;

	/**
	 * constructor kann nur abgeleitet werden
	 */
	protected function __construct()
	{

	}

	/**
	 * @return void
	 */
	private final function __clone()
	{

	}

	/**
	 * @static
	 * @return Registry
	 */
	public static function getInstance()
	{
		if (self::$uniqueInstance === null)
		{
			self::$uniqueInstance = new Registry();
		}

		return self::$uniqueInstance;
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @return void
	 */
	public function __set($key, $value)
	{
		$this->set($key, $value, self::KEY_REGISTRY);
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this->get($key, self::KEY_REGISTRY);
	}

	/**
	 * @param string $key
	 * @return void
	 */
	public function __unset($key)
	{
		$this->unsetKey($key, self::KEY_REGISTRY);
	}


	/**
	 * @param string $key
	 * @return bool
	 */
	public function __isset($key)
	{
		return $this->hasKey($key, self::KEY_REGISTRY);
	}

	/**
	 * @param Request $request
	 * @return void
	 */
	public function setRequest(Request $request)
	{
		$this->set(self::KEY_REQUEST, $request, self::KEY_SYSTEM);
	}


	/**
	 * @return Request
	 */
	public function getRequest()
	{
		return $this->get(self::KEY_REQUEST, self::KEY_SYSTEM);
	}


	/**
	 * @return bool
	 */
	public function hasRequest()
	{
		return $this->hasKey(self::KEY_REQUEST, self::KEY_SYSTEM);
	}

	/**
	 * @param Response $response
	 * @return void
	 */
	public function setResponse(Response $response)
	{
		$this->set(self::KEY_RESPONSE, $response, self::KEY_SYSTEM);
	}


	/**
	 * @return Response
	 */
	public function getResponse()
	{
		return $this->get(self::KEY_RESPONSE, self::KEY_SYSTEM);
	}


	/**
	 * @return bool
	 */
	public function hasResponse()
	{
		return $this->hasKey(self::KEY_RESPONSE, self::KEY_SYSTEM);
	}

	/**
	 * @param Database $database
	 * @return void
	 */
	public function setDatabase(Database $database)
	{
		$this->set(self::KEY_DATABASE, $database, self::KEY_SYSTEM);
	}

	/**
	 * @return Database
	 */
	public function getDatabase()
	{
		return $this->get(self::KEY_DATABASE, self::KEY_SYSTEM);
	}


	/**
	 * @return bool
	 */
	public function hasDatabase()
	{
		return $this->hasKey(self::KEY_DATABASE, self::KEY_SYSTEM);
	}

	/**
	 * @param Template $template
	 * @return void
	 */
	public function setTemplate(Template $template)
	{
		$this->set(self::KEY_TEMPLATE, $template, self::KEY_SYSTEM);
	}

	/**
	 * @return Template
	 */
	public function getTemplate()
	{
		return $this->get(self::KEY_TEMPLATE, self::KEY_SYSTEM);
	}

	/**
	 * @return bool
	 */
	public function hasTemplate()
	{
		return $this->hasKey(self::KEY_TEMPLATE, self::KEY_SYSTEM);
	}

	/**
	 * @param Session $session
	 * @return void
	 */
	public function setSession(Session $session)
	{
		$this->set(self::KEY_SESSION, $session, self::KEY_SYSTEM);
	}


	/**
	 * @return Session
	 */
	public function getSession()
	{
		return $this->get(self::KEY_SESSION, self::KEY_SYSTEM);
	}


	/**
	 * @return bool
	 */
	public function hasSession()
	{
		return $this->hasKey(self::KEY_SESSION, self::KEY_SYSTEM);
	}

	/**
	 * Warum ist das public? und muss es static sein?
	 * -- Vadim
	 * Wegen Unittests, damit sie resetet werden kann
	 * -- Mardl
	 *
	 * @return void
	 */
	public static function reset()
	{
		self::$uniqueInstance = null;
	}

	/**
	 * @param EventDispatcher $eventDispatcher
	 * @return void
	 */
	public function setEventDispatcher(EventDispatcher $eventDispatcher)
	{
		$this->set(self::KEY_EVENTDISPATCHER, $eventDispatcher, self::KEY_SYSTEM);
	}

	/**
	 * @return EventDispatcher
	 */
	public function getEventDispatcher()
	{
		return $this->get(self::KEY_EVENTDISPATCHER, self::KEY_SYSTEM);
	}

	/**
	 * @return bool
	 */
	public function hasEventDispatcher()
	{
		return $this->hasKey(self::KEY_EVENTDISPATCHER, self::KEY_SYSTEM);
	}

	/* Private Funktionen zum Zugriff der Interzeptoren*/

	/**
	 * @param string $key
	 * @param string $const
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
	 * @param string $key
	 * @param string $const
	 * @return void
	 */
	protected function unsetKey($key, $const)
	{
		if ($this->hasKey($key, $const))
		{
			unset($this->values[$const][$key]);
		}
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @param string $const
	 * @return void
	 */
	protected function set($key, $value, $const)
	{
		$this->values[$const][$key] = $value;
	}

	/**
	 * @param string $key
	 * @param string $const
	 * @return mixed
	 * @throws \Exception
	 */
	protected function get($key, $const)
	{
		if ($this->hasKey($key, $const))
		{
			return $this->values[$const][$key];
		}

		throw new \Exception("Registry-Value '{$key}' ist nicht gesetzt!");
	}

	/**
	 * @param string $classname
	 * @return void
	 */
	public function setLogger4php($classname)
	{
		$this->set('logger4php', $classname, self::KEY_SYSTEM);
	}

	/**
	 * Pfad zu log4php/Logger.php muss inkludiert sein!
	 *
	 * @param string|object $name
	 * @param bool          $changeSlashes
	 * @param string        $loggerInstance
	 * @return \Logger
	 */
	public function getLogger($name, $changeSlashes = true, $loggerInstance = null)
	{
		if (is_object($name))
		{
			$name = get_class($name);
		}
		if ($changeSlashes)
		{
			$name = str_replace('\\', '.', $name);
		}
		if (is_null($loggerInstance))
		{
			$loggerInstance = '\Logger';

			if ($this->hasKey('logger4php', self::KEY_SYSTEM))
			{
				$loggerInstance = $this->get('logger4php', self::KEY_SYSTEM);
			}
		}
		return $loggerInstance::getLogger($name);
	}


}
