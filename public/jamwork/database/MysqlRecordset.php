<?php

namespace jamwork\database;

use jamwork\common\Registry;

class MysqlRecordset implements Recordset
{
	protected $query = null;
	protected $result = false;
	
	public function execute(Query $query)
	{
		$this->query = $query;
		$sql = $this->query->get();
		$this->result = mysql_query($sql);
		
		try
		{
			$debugger = Registry::getInstance()->debugger;
			
			if ($debugger)
			{
				if (@$debugger->queries)
				{
					$debugger->queries[] = $sql;
				}
				else
				{
					$debugger->queries = array($sql);
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