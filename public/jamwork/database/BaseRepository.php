<?php

namespace jamwork\database;

use \jamwork\common\Registry;
use \jamwork\database\BaseModel;

abstract class BaseRepository
{
	private $database = null;
	private $models = array();
	
	abstract function getById($id);

	/**
	 * @return \jamwork\common\Registry
	 */
	protected function getRegistry()
	{
		return Registry::getInstance();
	}

	/**
	 * @return MysqlDatabase
	 */
	protected function getDatabase()
	{
		return $this->getRegistry()->getDatabase();
	}

	/**
	 * @return MysqlQuery
	 */
	protected function getQuery()
	{
		$query = $this->getDatabase()->newQuery();
		return $query;
	}

	/**
	 * @return MysqlRecordset
	 */
	protected function getRecordset()
	{
		$recordset = $this->getDatabase()->newRecordSet();
		return $recordset;
	}

	/**
	 * @param mixed $modelOrId
	 * @return null|int
	 */
	protected function findId($modelOrId)
	{
		if($modelOrId instanceof BaseModel)
		{
			return $modelOrId->getId();
		}
		
		if(is_numeric($modelOrId))
		{
			return $modelOrId;
		}
		
		return null;
	}
	
	protected function getModel($modelClass, $query)
	{
		$key = $this->getKey($query);
		
		if(isset($this->models[$key]))
		{
			return $this->models[$key];
		}
				
		$recordset = $this->getRecordset()->execute($query);
		$rec = $recordset->get();
		$this->models[$key] = new $modelClass( $rec );
		return $this->models[$key];
	}
	
	private function getKey($query)
	{
		$sql = $query->get();
		return md5($sql);
	}
	
	protected function getModels($modelClass, $query)
	{
		$key = $this->getKey($query);
		if(isset($this->models[$key]))
		{
			return $this->models[$key];
		}
		
		$recordset = $this->getRecordset()->execute($query);
		$models = array();
		while($rec = $recordset->get())
		{
			$models[] = new $modelClass( $rec );
		}
		
		$this->models[$key] = $models;
		return $this->models[$key];
	}
}