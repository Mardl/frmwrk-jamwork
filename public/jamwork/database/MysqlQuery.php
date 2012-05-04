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
	
	public function addWhere($field, $value, $op = '=')
	{
		if (is_numeric($value))
		{
			$string = $field.' '.$op.' '.mysql_real_escape_string($value);
		}
		
		if (is_string($value))
		{
			$string = $field.' '.$op.' "'.mysql_real_escape_string($value).'"';
		}
		
		return $this->where($string);
	}
	
	public function andWhere($field, $value, $op = '=')
	{
		$string = $this->clause.' AND ';
		
		if (is_numeric($value))
		{
			$string .= $field.' '.$op.' '.mysql_real_escape_string($value);
		}
		
		if (is_string($value))
		{
			$string .= $field.' '.$op.' "'.mysql_real_escape_string($value).'"';
		}
		
		return $this->where($string);
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
	
	public function limit($offset, $limit=null)
	{
		$this->limit = array();
		$this->limit[] = $offset;
		if($limit !== null)
		{
			$this->limit[] = $limit;
		}
		return $this;
	}
	
	public function join($join, $type = 'LEFT')
	{
		$this->jointable[] = array($join, $type);
		return $this;
	}
	
	public function innerJoin($join)
	{
		return $this->join($join, 'INNER');
	}
	
	public function on($joinOn)
	{
		$this->joinon[] = $joinOn;
		return $this;
	}
	
	public function setQueryOnce($queryString)
	{	
		if (substr(strtoupper(trim($queryString)),0,6) == 'SELECT')
		{
			throw new \ErrorException('Benutze den Querybuilder für SELECT-Statements');
		}
		
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
			 * verwendet man das Objekt mehrfach f�r verschidene Queries, so sollte man mehrere Objekte haben!
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
				$query .= " ".$value[1]." JOIN ".$value[0]." ON ".$this->joinon[$key]." ";
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
		return $query;//return $this->database->clear($query);
	}

}