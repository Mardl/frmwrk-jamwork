<?php

namespace jamwork\database;

use \jamwork\common\Registry;
use \jamwork\database\BaseModel;

abstract class BaseRepository
{
	protected $database = null;
	protected $models = array();
	
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
		
		return 0;
	}
	
	protected function getModel($modelClass, $query, $force=false)
	{
		$key = $this->getKey($query);
		
		if(isset($this->models[$key]) && !$force)
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
	
	protected function getModels($modelClass, $query, $force=false)
	{
		$key = $this->getKey($query);
		if(array_key_exists($key, $this->models) && !$force) {
			return $this->models[$key];
		}

		$recordset = $this->getRecordset()->execute($query);
		$models = array();
		while($rec = $recordset->get()) {
			$models[] = new $modelClass( $rec );
		}
		
		$this->models[$key] = $models;
		return $this->models[$key];
	}
}