<?php

namespace jamwork\database;

use jamwork\common\Registry;

class MysqlRecordset implements Recordset
{
	protected $query = null;
	protected $result = false;


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
	 *
	 * @param $stmtString Statement das ausgeführt werden soll
	 * @return MysqlRecordset
	 */
	private function executeStmt($stmtString)
	{
		$this->result = mysql_query($stmtString);

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
		}
		catch (\Exception $e)
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


	public function isSuccessful()
	{
		if($this->result)
		{
			return true;
		}
		return false;
	}
	
	public function count()
	{
		if (!$this->result)
		{
			return false;
		}
		return mysql_num_rows($this->result);
	}
	
	public function get()
	{
		if (!$this->result)
		{
			return false;
		}
		return mysql_fetch_assoc($this->result);
	}
}