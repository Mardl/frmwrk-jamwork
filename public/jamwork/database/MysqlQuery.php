<?php

namespace jamwork\database;

use \jamwork\database\Database;

class MysqlQuery implements Query
{
	protected $table = '';
	protected $fields = '*';
	protected $clause = '';
	protected $order = '';
	protected $groupby = '';
	protected $jointable = array();
	protected $limit = array();
	protected $joinon = array();
	protected $ownQuery = '';
	
	private $database = null;
	
	public function __construct(Database $database)
	{
		$this->database = $database;
	}
	
	public function from($table)
	{
		$this->table = $table;
		return $this;
	}
	public function select($fields)
	{
		$this->fields = $fields;
		return $this;
	}
	public function where($clause)
	{
		$this->clause = $clause;
		return $this;
	}
	
	public function orderBy($order)
	{
		$this->order = $order;
		return $this;
	}
	
	public function groupBy($groupby)
	{
		$this->groupby = $groupby;
		return $this;
	}
	
	public function limit($start, $limit=null)
	{
		$this->limit = array();
		$this->limit[] = $start;
		if($limit !== null)
		{
			$this->limit[] = $limit;
		}
		return $this;
	}
	
	public function join($join)
	{
		$this->jointable[] = $join;
		return $this;
	}
	
	public function on($joinOn)
	{
		$this->joinon[] = $joinOn;
		return $this;
	}
	
	public function setQueryOnce($queryString)
	{	
		$this->ownQuery = $queryString;
		return $this;
	}
	
	public function get()
	{
		if(!empty($this->ownQuery))
		{
			$query = $this->ownQuery;
			/* 
			 * Clearing des onceQuery entfernt -> jedes Objekt hat EINE Aufgabe
			 * verwendet man das Objekt mehrfach für verschidene Queries, so sollte man mehrere Objekte haben!
			 * Zitat: Vadim am 29.02.2012
			 */
			// $this->ownQuery = '';
			return $query;
		}
		$query = "SELECT ";
		if (is_array($this->fields))
		{
			$query .= implode (',', $this->fields);
		}
		else 
		{
			$query .= $this->fields;
		}
		$query .= " FROM ".$this->table;
		if ( !empty($this->jointable))
		{
			foreach($this->jointable as $key => $value)
			{
				$query .= " LEFT JOIN ".$value." ON ".$this->joinon[$key]." ";
			}
		}

		if ( !empty($this->clause) ) 
		{
			$query .= " WHERE ".$this->clause;
		}
		if ( !empty($this->groupby) ) 
		{
			$query .= " GROUP BY ".$this->groupby;
		}
		if ( !empty($this->order) ) 
		{
			$query .= " ORDER BY ".$this->order;
		}		
		if ( !empty($this->limit) ) 
		{
			$query .= " LIMIT ".implode(', ',$this->limit);
		}
		return $this->database->clear($query);
	}

}