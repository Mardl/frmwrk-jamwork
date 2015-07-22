<?php

namespace jamwork\helper;

/**
 * Analysiert einen in den Construktor übergebenen String, ob es sich dabei um eine Url handelt und liefert diverse Url-Parts
 *
 * @author Vadim Justus <vadim@dreiwerken.de>
 */
class UrlParser
{

	/**
	 * Is URL
	 * @param boolean $regMatch
	 */
	private $regMatch = false;

	/**
	 * Origin String
	 * @param string $originString
	 */
	private $originString = '';

	/**
	 * Array mit Informationen über die URL
	 * @param array $info
	 */
	private $info = array();

	/**
	 * Array mit Query-Informationen
	 * @param array $queryInfo
	 */
	private $queryInfo = array();

	/**
	 * Konstruktor
	 * @param string $url
	 */
	function __construct($url)
	{
		/*
		$regex = "((https?|ftp)\:\/\/)?"; // SCHEME
		$regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
		$regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
		$regex .= "(\:[0-9]{2,5})?"; // Port
		$regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
		$regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
		$regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor
		
		$this->regMatch = preg_match("/^$regex$/", $url, $this->info);
		*/

		$this->originString = $url;
		$this->info = parse_url($url);

		$scheme = $this->getScheme();
		$host = $this->getHost();
		if (!empty($scheme) && !empty($host))
		{
			$this->regMatch = true;
		}
	}

	private function get($key)
	{
		if (isset($this->info[$key]))
		{
			return $this->info[$key];
		}

		return '';
	}

	private function queryGet($key, $def = '')
	{
		$query = $this->getQuery();
		if (!empty($query) && empty($this->queryInfo))
		{
			$this->readQuery($query);
		}

		if (isset($this->queryInfo[$key]))
		{
			return $this->queryInfo[$key];
		}

		return $def;
	}

	private function readQuery($queryString)
	{
		$queries = explode('&', $queryString);
		foreach ($queries as $query)
		{
			$queryParts = explode('=', $query);
			$key = $queryParts[0];
			$value = $queryParts[1];

			$this->queryInfo[$key] = $value;
		}
	}

	/**
	 * Liefert true zurück, wenn den übergeben String tatsächtlich eine Url ist.
	 * @return boolean
	 */
	public function isUrl()
	{
		return $this->regMatch;
	}

	/**
	 * Liefert den origialen String zurück
	 * @return string
	 */
	public function getString()
	{
		return $this->originString;
	}

	/**
	 * Liefert das Scheme zurück - z.B. http
	 * @return string
	 */
	public function getScheme()
	{
		return $this->get('scheme');
	}

	/**
	 * Liefert den Host zurück
	 * @return string
	 */
	public function getHost()
	{
		return $this->get('host');
	}

	/**
	 * Liefert den Hash-Tag der URL zurück
	 * @return string
	 */
	public function getFragment()
	{
		return $this->get('fragment');
	}

	/**
	 * Liefert den User der URL zurück
	 * @return string
	 */
	public function getUser()
	{
		return $this->get('user');
	}

	/**
	 * Liefert das Passwort der URL zurück
	 * @return string
	 */
	public function getPass()
	{
		return $this->get('pass');
	}

	/**
	 * Liefert den Query der URL zurück
	 * @return string
	 */
	public function getQuery()
	{
		return $this->get('query');
	}

	/**
	 * Liefert den Pfad der URL zurück
	 * @return string
	 */
	public function getPath()
	{
		return $this->get('path');
	}

	/**
	 * überprüft, ob der Query der URL einen bestimmten Key hat
	 * @param string $key
	 * @return boolean
	 */
	public function hasQueryKey($key)
	{
		$query = $this->queryGet($key, false);

		return $query !== false;
	}

	/**
	 * Liefert den Value eines Query-Eintrags
	 * @param string $key
	 * @return string
	 */
	public function getQueryData($key)
	{
		return $this->queryGet($key);
	}
}

?>