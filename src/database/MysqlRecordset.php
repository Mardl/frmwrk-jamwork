<?php

namespace jamwork\database;

use jamwork\common\Registry;

/**
 * Class MysqlRecordset
 *
 * @category Jamwork
 * @package  Jamwork\database
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
class MysqlRecordset implements Recordset
{

	/**
	 * @var null
	 */
	protected $query = null;

	/**
	 * @var int
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
	 * Führt das übergeben Query Objekt aus
	 *
	 * @param Query $query
	 * @return Recordset
	 */
	public function execute(Query $query)
	{
		$this->query = $query;
		$sql = $this->query->get();

		return $this->executeStmt($sql);
	}

	/**
	 * Führt den übergebenen Statement String aus
	 * @param string $stmtString Statement das ausgeführt werden soll
	 * @return MysqlRecordset
	 */
	private function executeStmt($stmtString)
	{
		$this->result = mysql_query($stmtString);

		if (!$this->result)
		{
			$this->errorMessage = mysql_error();
			$this->errorNumber = mysql_errno();
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
	 * @deprecated
	 * @return bool
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

		return mysql_num_rows($this->result);
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

		return mysql_fetch_assoc($this->result);
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