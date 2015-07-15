<?php

namespace jamwork\database;

use jamwork\common\Registry;
use PDO;
use PDOStatement;

/**
 * Class MssqlRecordset
 *
 * @category Jamwork
 * @package  Jamwork\database
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
class MssqlRecordset implements Recordset
{

	/**
	 * @var MssqlQuery
	 */
	protected $query = null;

	/**
	 * @var PDOStatement
	 */
	protected $result = false;

	/**
	 * @var string
	 */
	protected $errorMessage = '';

	/**
	 * @var int
	 */
	protected $errorNumber = -1;

	/**
	 * @var MssqlDatabase
	 */
	private $database = null;

	/** @var array */
	protected $allData = array();

	/** @var integer */
	protected $dataPointer = 0;

	/**
	 * @param Database $database
	 */
	public function __construct(Database $database)
	{
		$this->database = $database;
	}

	/**
	 * Führt das übergeben Query Objekt aus
	 *
	 * @param Query $query
	 * @return Recordset
	 */
	public function execute(Query $query)
	{
		$this->query = $query;

		return $this->executeStmt();
	}

	/**
	 * Führt den übergebenen Statement String aus
	 * @throws \PDOException
	 * @return MssqlRecordset
	 */
	private function executeStmt()
	{
		$this->result = false;
		$stmtString = $this->query->get();
		$this->allData = array();
		$this->dataPointer = -1;
		//syslog(LOG_INFO, $stmtString);

		try
		{
			if ($this->query->isQueryOnce())
			{
				$this->result = $this->database->getConnection()->query($stmtString);
			}
			else
			{
				$keyValuePair = $this->query->getBindValues();
				$stmt = $this->database->getConnection()->prepare($stmtString, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
				foreach ($keyValuePair as $key => $value)
				{
					$stmt->bindValue($key, iconv('UTF-8', 'ISO8859-1', $value)); // UTF 8 kann mssql nicht richtig
				}
				if ($stmt->execute())
				{
					$this->result = $stmt;
				}

			}

			$errorInfo = $this->result->errorInfo();
			if (isset($errorInfo[4]) && ! empty($errorInfo[4]))
			{
				throw new \PDOException($errorInfo[2]);
			}

			$this->allData = $this->result->fetchAll();

		} catch (\PDOException  $e)
		{
			$stmtPreparedString = $this->query->getPreparedStatement();

			$this->errorMessage = $e->getMessage();
			$this->errorNumber = $e->getCode();

			throw new \PDOException($stmtPreparedString." ".$e->getMessage());//, (int) $this->errorNumber, $e);
		}

		try
		{
			$debugger = Registry::getInstance()->debugger;

			if ($debugger)
			{
				if (isset($debugger->queries))
				{
					$debugger->queries[] = $stmtString;
				}
				else
				{
					$debugger->queries = array($stmtString);
				}
			}
		} catch (\Exception $e)
		{
			// Nothing to do, debugger not initiated
		}

		return $this;
	}

	/**
	 * @return bool
	 * @deprecated
	 */
	public function isSuccessfull()
	{
		return $this->isSuccessful();
	}


	/**
	 * @return bool
	 */
	public function isSuccessful()
	{
		if ($this->result)
		{
			return true;
		}

		return false;
	}

	/**
	 * @return bool|int
	 */
	public function count()
	{
		if (!$this->result)
		{
			return false;
		}

		return count($this->allData);
	}

	/**
	 * @return array|bool
	 */
	public function get()
	{
		if (!$this->result || count($this->allData) == 0)
		{
			return false;
		}
		$this->dataPointer++;
		if ($this->dataPointer >= count($this->allData))
		{
			return false;
		}
		return $this->allData[$this->dataPointer];
	}

	/**
	 * @return string
	 */
	public function getErrorMessage()
	{
		return $this->errorMessage;
	}

	/**
	 * @return int
	 */
	public function getErrorNumber()
	{
		return $this->errorNumber;
	}
}