<?php

namespace jamwork\database;

use \jamwork\common\Registry;

class BaseModel
{
	protected $record = array();
	protected $prefix = '';
	protected $change = false;
	protected $insert = false;
	protected $new = false;
	protected $dontSave = false;
	
	public function __construct($record=array())
	{
		if(empty($record))
		{
			$this->insert = true;
			$this->new = true;
		}
		$this->record = $record;
	}
	
	public function __destruct()
	{
		$this->save();
	}
	
	public function __clone()
	{
		$this->new = true;
		$this->insert = true;
		$this->change = true;
		$this->deleteRecordValue('id');
	}

	public function dontSave($bool=true) {
		$this->dontSave = $bool;
	}
	
	public function getRecord()
	{
		return $this->record;
	}
	
	public function getId()
	{
		if($this->get('id') === null)
		{
			$this->save();
		}
		return $this->get('id');
	}
	
	public function delete()
	{
		if($this->isNew() || $this->isEmpty())
		{
			$this->new = false;
			$this->change = false;
			$this->insert = false;
			return true;
		}
		
		$database = Registry::getInstance()->getDatabase();
		return $database->delete($this->table, $this->record);
	}
	
	public function save()
	{
		$eventDispatcher = Registry::getInstance()->getEventdispatcher();

		if(!$this->isEmpty() && $this->hasChange())
		{
			$eventDispatcher->triggerEvent('onBeforeModelSave', $this);
			$this->change = false;
			$database = Registry::getInstance()->getDatabase();
			if($this->isInsert())
			{
				$id = $database->insert($this->table, $this->record);
				$this->set('id', $id);
				$this->insert = false;
				$eventDispatcher->triggerEvent('onModelSaveInsert', $this);
				return $id;
			}
			$eventDispatcher->triggerEvent('onModelSaveUpdate', $this);
			return $database->update($this->table, $this->record);
		}
		
		$eventDispatcher->triggerEvent('onModelSaveError', $this);
	}
	
	public function isInsert()
	{
		return $this->insert == true;
	}
	
	public function isNew()
	{
		return $this->new == true;
	}
	
	public function isEmpty()
	{
		return empty($this->record);
	}
	
	protected function has($key)
	{
		return isset($this->record[$this->prefix.$key]);
	}
	
	protected function deleteRecordValue($key)
	{
		$this->record[$this->prefix.$key] = 0;
	}
	
	public function get($key, $def=null)
	{
		if($this->has($key))
		{
			return $this->record[$this->prefix.$key];
		}
		return $def;
	}
	
	public function set($key, $value)
	{
		$this->change = true;
		$this->record[$this->prefix.$key] = $value;
		return $this;
	}
	
	protected function hasChange()
	{
		return $this->change == true && $this->dontSave === false;
	}
}