<?php

namespace jamwork\database;

use \jamwork\database\Database;

/**
 * Class MysqlQuery
 *
 * @category Jamwork
 * @package  Jamwork\database
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
class MysqlQuery implements Query
{

	protected $queryTyp = 1;
	protected $distinct = false;
	protected $table = '';
	protected $sets = '';
	protected $fields = '*';
	protected $clause = '';
	protected $having = '';
	protected $order = '';
	protected $groupby = '';
	protected $jointable = array();
	protected $limit = array();
	protected $joinon = array();
	protected $ownQuery = '';
	protected $lastQuery = '';
	protected $openClosure = false;
	protected $closeClosure = false;

	/**
	 * @var Database
	 */
	private $database = null;

	/**
	 * @param Database $database
	 */
	public function __construct(Database $database)
	{
		$this->database = $database;
	}

	/**
	 * @param string $table
	 * @return MysqlQuery|Query
	 */
	public function from($table)
	{
		$this->table = $table;

		return $this;
	}

	/**
	 * @param string $fields
	 * @return MysqlQuery|Query
	 */
	public function select($fields)
	{
		$this->setSelectType();
		$this->fields = $fields;

		return $this;
	}

	/**
	 * @param string $table
	 * @return MysqlQuery
	 */
	public function update($table)
	{
		$this->setUpdateType();
		$this->table = $table;

		return $this;
	}

	/**
	 * @param string $fieldName
	 * @param string $value
	 * @return MysqlQuery
	 */
	public function set($fieldName, $value)
	{
		$this->sets[$fieldName] = $value;

		return $this;
	}

	/**
	 * @param string $clause
	 * @return MysqlQuery|Query
	 */
	public function where($clause)
	{
		$this->clause = $clause;

		return $this;
	}

	/**
	 * @return MysqlQuery
	 */
	public function openClosure()
	{
		$this->openClosure = true;

		return $this;
	}

	/**
	 * @return MysqlQuery
	 */
	public function closeClosure()
	{
		$this->clause .= ' )';

		return $this;
	}

	/**
	 * @param bool $distinct
	 * @return MysqlQuery|void
	 */
	public function distinct($distinct = true)
	{
		$this->distinct = $distinct;

		return $this;
	}

	/**
	 * @return void
	 */
	private function setSelectType()
	{
		$this->queryTyp = 1;
	}

	/**
	 * @return void
	 */
	private function setUpdateType()
	{
		$this->queryTyp = 2;
	}

	/**
	 * @return bool
	 * @deprecated
	 */
	protected function isSelectStatement()
	{
		return ($this->queryTyp == 1);
	}

	/**
	 * @return bool
	 */
	protected function isUpdateStatement()
	{
		return ($this->queryTyp == 2);
	}

	/**
	 * Fügt eine neue WHERE-Klausel hinzu und escaped jeden Parameter
	 *
	 * @param string         $field Feld für die Bedingung
	 * @param string|integer $value Vergleichswert
	 * @param string         $op    Optionaler Operator, default "="
	 * @param string         $concat
	 * @return $this|MysqlQuery|Query|string
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 */
	public function addWhere($field, $value, $op = '=', $concat = 'AND')
	{
		$string = '';

		if (!empty($this->clause) || $this->openClosure)
		{
			$string = $this->concatToClause($this->clause, $concat, $this->openClosure);
		}

		if (is_null($value))
		{
			//throw new \ErrorException('ACHTUNG: Aufruf von AddWhere mit Null Value! Bitte überprüfen!');
			throw new \Exception('ACHTUNG: Aufruf von AddWhere mit Null Value! Bitte überprüfen!');
		}
		elseif (is_numeric($value))
		{
			$string .= $field . ' ' . $op . ' ' . mysql_real_escape_string($value);
		}
		elseif (is_string($value))
		{
			$string .= $field . ' ' . $op . ' "' . mysql_real_escape_string($value) . '"';
		}
		elseif (is_array($value))
		{
			$string .= $this->in($field, $value);
		}
		else
		{
			throw new \InvalidArgumentException('ACHTUNG: Aufruf von AddWhere mit unbekannten Parameter! Bitte überprüfen!');
		}


		return $this->where($string);
	}

	/**
	 * @param string $field
	 * @param string $op
	 * @param string $concat
	 * @return MysqlQuery|Query
	 */
	public function addWhereIsNull($field, $op = 'IS', $concat = 'AND')
	{
		$string = '';

		if (!empty($this->clause) || $this->openClosure)
		{
			$string = $this->concatToClause($this->clause, $concat, $this->openClosure);
			//$string = $this->clause.' '.$concat.' ';
		}

		$string .= $field . ' ' . $op . ' NULL ';

		return $this->where($string);
	}

	/**
	 * @param string     $field
	 * @param string|int $value
	 * @param string     $op
	 * @param string     $concat
	 * @return MysqlQuery|Query
	 */
	public function addWhereFunc($field, $value, $op = '=', $concat = 'AND')
	{
		$string = '';

		if (!empty($this->clause) || $this->openClosure)
		{
			$string = $this->concatToClause($this->clause, $concat, $this->openClosure);
			//$string = $this->clause.' '.$concat.' ';
		}

		$string .= $field . ' ' . $op . ' ' . mysql_real_escape_string($value) . ' ';

		return $this->where($string);
	}

	/**
	 * @param string     $field    betroffenens Feld
	 * @param string|int $valueMin Wert von
	 * @param string|int $valueMax Wert bis
	 * @param string     $concat   default 'and'
	 * @return MysqlQuery|Query
	 * @throws \InvalidArgumentException
	 */
	public function addWhereBetween($field, $valueMin, $valueMax, $concat = 'AND')
	{
		if (is_numeric($valueMin) != is_numeric($valueMax))
		{
			throw new \InvalidArgumentException('Min- und Maxwert muss bei between vom selben Typen sein!');
		}


		$string = '';

		if (!empty($this->clause) || $this->openClosure)
		{
			$string = $this->concatToClause($this->clause, $concat, $this->openClosure);
		}

		if (is_numeric($valueMin))
		{
			$string .= $field . ' between ' . mysql_real_escape_string($valueMin) . ' AND ' . mysql_real_escape_string($valueMax);
		}
		else
		{
			$string .= $field . ' between "' . mysql_real_escape_string($valueMin) . '" AND "' . mysql_real_escape_string($valueMax) . '"';
		}

		return $this->where($string);
	}

	/**
	 * @param string           $field
	 * @param string|int|array $value
	 * @param string           $phraseOrder
	 * @param string           $concat
	 * @return MysqlQuery|Query
	 */
	public function addWhereLike($field, $value, $phraseOrder = '%%%s%%', $concat = 'AND')
	{
		$string = '';

		if (!empty($this->clause) || $this->openClosure)
		{
			$string = $this->concatToClause($this->clause, $concat, $this->openClosure);
		}

		$string .= $field . ' LIKE "' . sprintf($phraseOrder, mysql_real_escape_string($value)) . '" ';

		return $this->where($string);
	}


	/**
	 * @param string $field
	 * @param string $value
	 * @return MysqlQuery|Query
	 */
	public function innerStatement($field, $value)
	{
		return $this->addWhereFunc($field, '(' . $value . ')', 'IN');
	}

	/**
	 * @param string           $field
	 * @param string|int|array $value
	 * @param string           $op
	 * @param string           $concat
	 * @return MysqlQuery|Query|string
	 * @throws \InvalidArgumentException
	 */
	public function addHaving($field, $value, $op = '=', $concat = 'AND')
	{
		$string = '';

		if (!empty($this->having))
		{
			$string = $this->having . ' ' . $concat . ' ';
		}

		if (is_null($value))
		{
			$string .= $field . ' ' . $op . ' NULL';
		}
		else
		{
			if (is_numeric($value))
			{
				$string .= $field . ' ' . $op . ' ' . mysql_real_escape_string($value);
			}
			else
			{
				if (is_string($value))
				{
					$string .= $field . ' ' . $op . ' "' . mysql_real_escape_string($value) . '"';
				}
				else
				{
					throw new \InvalidArgumentException('ACHTUNG: Aufruf von addHaving mit unbekannten Parameter! Bitte überprüfen!');
				}
			}
		}

		$this->having = $string;

		return $this;
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
		$string = $field . ' IN (';

		$string .= implode(
			',',
			array_map(
				function ($item)
				{
					if (is_string($item))
					{
						return "'" . mysql_real_escape_string($item) . "'";
					}

					return mysql_real_escape_string($item);
				},
				$values
			)
		);

		$string .= ')';

		return $string;
	}

	/**
	 * @param string $order
	 * @return MysqlQuery|Query
	 */
	public function orderBy($order)
	{
		$this->order = $order;

		return $this;
	}

	/**
	 * @param string $groupby
	 * @return MysqlQuery|Query
	 */
	public function groupBy($groupby)
	{
		$this->groupby = $groupby;

		return $this;
	}

	/**
	 * @param int      $offset
	 * @param int|null $limit
	 * @return MysqlQuery|Query
	 */
	public function limit($offset, $limit = null)
	{
		$this->limit = array();
		$this->limit[] = $offset;
		if ($limit !== null)
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

	/**
	 * @param string $joinOn
	 * @return MysqlQuery|Query
	 */
	public function on($joinOn)
	{
		$this->joinon[] = $joinOn;

		return $this;
	}

	/**
	 * Hinterlegt ein vorgefertigtes SQL-Statement.
	 *
	 * @param string $queryString
	 * @return $this|Query
	 * @throws \ErrorException Wenn ein SELECT-Statement ausgeführt werden soll
	 */
	public function setQueryOnce($queryString)
	{
		$checkString = strtoupper(trim($queryString));
		if (substr($checkString, 0, 6) == 'SELECT' && strpos($checkString, "UNION") === false)
		{
			throw new \ErrorException('Benutze den Querybuilder für SELECT-Statements');
		}

		$this->ownQuery = $queryString;

		return $this;
	}

	/**
	 * @return Query|string
	 */
	public function get()
	{
		if ($this->isUpdateStatement())
		{
			return $this->getUpdate();
		}

		return $this->getSelect();
	}

	/**
	 * @return string
	 */
	private function getSelect()
	{
		if (!empty($this->ownQuery))
		{
			$query = $this->ownQuery;
			/*
			 * Clearing des onceQuery entfernt -> jedes Objekt hat EINE Aufgabe
			 * verwendet man das Objekt mehrfach für verschidene Queries, so sollte man mehrere Objekte haben!
			 * Zitat: Vadim am 29.02.2012
			 */
			// $this->ownQuery = '';
			$this->lastQuery = str_split($query, 255);

			return $query;
		}
		$query = "SELECT ";

		if ($this->distinct)
		{
			$query .= "DISTINCT ";
		}

		if (is_array($this->fields))
		{
			$query .= implode(',', $this->fields);
		}
		else
		{
			$query .= $this->fields;
		}
		$query .= " FROM " . $this->table;
		if (!empty($this->jointable))
		{
			foreach ($this->jointable as $key => $value)
			{
				$query .= " " . $value[1] . " JOIN " . $value[0] . " ON " . $this->joinon[$key] . " ";
			}
		}

		if (!empty($this->clause))
		{
			$query .= " WHERE " . $this->clause;
		}
		if (!empty($this->groupby))
		{
			$query .= " GROUP BY " . $this->groupby;
		}
		if (!empty($this->having))
		{
			$query .= " HAVING " . $this->having;
		}
		if (!empty($this->order))
		{
			$query .= " ORDER BY " . $this->order;
		}
		if (!empty($this->limit))
		{
			$query .= " LIMIT " . implode(', ', $this->limit);
		}

		$query = $this->database->clear($query);

		$this->lastQuery = str_split($query, 255);

		return $query;
	}

	/**
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	private function getUpdate()
	{
		if (!empty($this->ownQuery))
		{
			$query = $this->ownQuery;

			return $query;
		}

		$query = "UPDATE " . $this->table . " SET ";

		if (is_array($this->sets))
		{
			$setValues = "";

			foreach ($this->sets as $key => $value)
			{
				$setValues .= empty($setValues) ? '' : ', ';
				if (is_null($value))
				{
					$setValues .= $key . " = NULL";
				}
				elseif (is_numeric($value))
				{
					$setValues .= $key . " = " . mysql_real_escape_string($value);
				}
				elseif (is_string($value))
				{
					$setValues .= $key . ' = "' . mysql_real_escape_string($value) . '"';
				}
				else
				{
					throw new \InvalidArgumentException('ACHTUNG: Aufruf von getUpdate mit unbekannten Parameter von addSet! Bitte überprüfen!');
				}
			}

			$query .= $setValues;
		}

		if (!empty($this->clause))
		{
			$query .= " WHERE " . $this->clause;
		}

		return $this->database->clear($query);
	}

	/**
	 * @param string $clause
	 * @param string $concat
	 * @param string $openClosure
	 * @return string
	 */
	private function concatToClause($clause, $concat, $openClosure)
	{
		$strOut = "";

		if ($openClosure)
		{
			if (!empty($this->clause))
			{
				$strOut = $clause . ' ' . $concat . ' (';
			}
			else
			{
				$strOut = $clause . ' (';
			}
			$this->openClosure = false;
		}
		else
		{
			if (!empty($this->clause))
			{
				$strOut = $clause . ' ' . $concat . ' ';
			}
		}

		return $strOut;
	}

}