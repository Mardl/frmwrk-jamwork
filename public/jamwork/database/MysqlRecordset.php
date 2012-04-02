<?php

namespace jamwork\database;

class MysqlRecordset implements Recordset
{
	protected $query = null;
	protected $result = false;
	
	public function execute(Query $query)
	{
		$this->query = $query;
		$sql = $this->query->get();
		$this->result = mysql_query($sql);
		return $this;
	}
	
	public function isSuccessfull()
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