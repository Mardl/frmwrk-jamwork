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
	
	/**
	 * Fügt eine neue WHERE-Klausel hinzu
	 * und escaped jeden Parameter
	 * 
	 * @param string         $field Feld für die Bedingung
	 * @param string|integer $value Vergleichswert
	 * @param string         $op    Optionaler Operator, default "="
	 * 
	 * @return MysqlQuery
	 */
	public function addWhere($field, $value, $op = '=')
	{
		$string = '';
		
		if (!empty($this->clause))
		{
			$string = $this->clause.' AND ';
		}	
			
		if (is_null($value))
		{
			return $this;
		}
		else if (is_numeric($value))
		{
			$string .= $field.' '.$op.' '.mysql_real_escape_string($value);
		}
		else if (is_string($value))
		{
			$string .= $field.' '.$op.' "'.mysql_real_escape_string($value).'"';
		}
		else if (is_array($value))
		{
			$string .= $this->in($field, $value);
		}
		else
		{
			return 'NULL';
		}
		
		return $this->where($string);
	}
	
	public function addWhereIsNull($field)
	{
		$string = '';
		
		if (!empty($this->clause))
		{
			$string = $this->clause.' AND ';
		}
		
		$string .= $field.' IS NULL ';
		
		return $this->where($string);
	}
	
	/**
	 * Präperiert für eine WHERE-Klausel eine Bedingung mit IN Operator
	 * und escaped jeden Parameter
	 * 
	 * @param string $field  Feld
	 * @param array  $values Array mit Integer-Werten
	 * 
	 * @return string
	 */
	public function in($field, array $values)
	{
		$string = $field.' IN (';
		
		$string .= implode(
			',',
			array_map(
				function($item)
				{
					if (is_string($item))
					{
						return "'".mysql_real_escape_string($item)."'";	
					}
					
					return mysql_real_escape_string($item);
				},
				$values
			)
		);
				
		$string .= ')';
		
		return $string;
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
	
	/**
	 * Fügt einen neuen Join hinzu
	 * 
	 * @param string $join Join-Table
	 * @param string $type Art des Joins, default LEFT
	 * 
	 * @return MysqlQuery
	 */
	public function join($join, $type = 'LEFT')
	{
		$this->jointable[] = array($join, $type);
		return $this;
	}
	
	/**
	 * Fügt einen Join hinzu vorgefertig auf INNER JOIN
	 * 
	 * @param string $join Join-Table
	 * 
	 * @return MysqlQuery
	 */
	public function innerJoin($join)
	{
		return $this->join($join, 'INNER');
	}
	
	/**
	 * Fügt einen Join hinzu vorgefertig auf LEFT JOIN
	 *
	 * @param string $join Join-Table
	 *
	 * @return MysqlQuery
	 */
	public function leftJoin($join)
	{
		return $this->join($join, 'LEFT');
	}
	
	public function on($joinOn)
	{
		$this->joinon[] = $joinOn;
		return $this;
	}
	
	/**
	 * Hinterlegt ein vorgefertigtes SQL-Statement.
	 * 
	 * @throws \ErrorException Wenn ein SELECT-Statement ausgeführt werden soll
	 * 
	 * @return MysqlQuery
	 */
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