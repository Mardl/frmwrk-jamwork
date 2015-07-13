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
				$stmt = $this->database->getConnection()->prepare($stmtString);
				foreach ($keyValuePair as $key => $value)
				{
					$stmt->bindValue($key, $value);
				}
				if ($stmt->execute())
				{
					$this->result = $stmt;
				}
			}
		} catch (\PDOException  $e)
		{
			$this->errorMessage = $e->getMessage();
			$this->errorNumber = $e->getCode();

			throw new \PDOException($this->errorMessage);
		}

		if (!$this->result && isset($stmt))
		{
			$this->errorMessage = $stmt->errorInfo();
			$this->errorNumber = $stmt->errorCode();
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

		return $this->result->rowCount();
	}

	/**
	 * @return array|bool
	 */
	public function get()
	{
		if (!$this->result)
		{
			return false;
		}

		return $this->result->fetch(PDO::FETCH_ASSOC);
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