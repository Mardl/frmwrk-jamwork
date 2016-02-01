<?php

namespace jamwork\database;

/**
 * Class PDOQuery
 *
 * @category Jamwork
 * @package  Jamwork\database
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
/**
 * Class PDOQuery
 *
 * @category Dreiwerken
 * @package  jamwork\database
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
class PDOQuery implements Query
{

	/**
	 * @var bool
	 */
	protected $distinct = false;
	/**
	 * @var string
	 */
	protected $table = '';
	/**
	 * @var string
	 */
	protected $fields = '*';
	/**
	 * @var string
	 */
	protected $clause = '';
	/**
	 * @var string
	 */
	protected $having = '';
	/**
	 * @var string
	 */
	protected $order = '';
	/**
	 * @var string
	 */
	protected $groupby = '';
	/**
	 * @var array
	 */
	protected $jointable = array();
	/**
	 * @var array
	 */
	protected $limit = array();
	/**
	 * @var array
	 */
	protected $joinon = array();
	/**
	 * @var string
	 */
	protected $ownQuery = '';
	/**
	 * @var string
	 */
	protected $lastQuery = '';
	/**
	 * @var bool
	 */
	protected $openClosure = false;
	/**
	 * @var bool
	 */
	protected $closeClosure = false;
	/**
	 * @var array
	 */
	protected $keyValuePair = array();

	/**
	 * @return string
	 */
	private function getPrepareField()
	{
		$nextOne = uniqid('fieldName');
		return $nextOne;
	}

	/**
	 * @param string $table
	 * @return PDOQuery|Query
	 */
	public function from($table)
	{
		$this->table = $table;

		return $this;
	}

	/**
	 * @param string $fields
	 * @return PDOQuery|Query
	 */
	public function select($fields)
	{
		$this->fields = $fields;

		return $this;
	}

	/**
	 * @param string $clause
	 * @return PDOQuery|Query
	 */
	public function where($clause)
	{
		$this->clause = $clause;

		return $this;
	}

	/**
	 * @return PDOQuery
	 */
	public function openClosure()
	{
		$this->openClosure = true;

		return $this;
	}

	/**
	 * @return PDOQuery
	 */
	public function closeClosure()
	{
		$this->clause .= ' )';

		return $this;
	}

	/**
	 * @param bool $distinct
	 * @return PDOQuery|void
	 */
	public function distinct($distinct = true)
	{
		$this->distinct = $distinct;

		return $this;
	}

    /**
     * Fügt eine neue WHERE-Klausel hinzu und escaped jeden Parameter
     *
     * @param string         $field Feld für die Bedingung
     * @param string|integer $value Vergleichswert
     * @param string         $op    Optionaler Operator, default "="
     * @param string         $concat
     * @param bool           $inOp
     * @return $this|PDOQuery|Query|string
     * @throws \Exception
     */
	public function addWhere($field, $value, $op = '=', $concat = 'AND', $inOp = true)
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
		elseif (is_string($value) || is_numeric($value))
		{
			$fieldUnique = $this->getPrepareField();
			$string .= $field . ' ' . $op . ' :' . $fieldUnique;
			$this->keyValuePair[':' . $fieldUnique] = $value;
		}
		elseif (is_array($value))
		{
			$string .= $this->in($field, $value, $inOp);
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
	 * @return PDOQuery|Query
	 */
	public function addWhereIsNull($field, $op = 'IS', $concat = 'AND')
	{
		$string = '';

		if (!empty($this->clause) || $this->openClosure)
		{
			$string = $this->concatToClause($this->clause, $concat, $this->openClosure);
		}

		$string .= $field . ' ' . $op . ' NULL ';

		return $this->where($string);
	}

	/**
	 * @param string     $field    betroffenens Feld
	 * @param string|int $valueMin Wert von
	 * @param string|int $valueMax Wert bis
	 * @param string     $concat   default 'and'
	 * @return PDOQuery|Query
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

		$fieldmin = $this->getPrepareField();
		$fieldmax = $this->getPrepareField();

		$string .= $field . ' between :' . $fieldmin . ' AND :' . $fieldmax . ' ';

		$this->keyValuePair[':' . $fieldmin] = $valueMin;
		$this->keyValuePair[':' . $fieldmax] = $valueMax;

		return $this->where($string);
	}

	/**
	 * @param string           $field
	 * @param string|int|array $value
	 * @param string           $phraseOrder
	 * @param string           $concat
	 * @return PDOQuery|Query
	 */
	public function addWhereLike($field, $value, $phraseOrder = '%%%s%%', $concat = 'AND')
	{
		$string = '';

		if (!empty($this->clause) || $this->openClosure)
		{
			$string = $this->concatToClause($this->clause, $concat, $this->openClosure);
		}

		$fieldUnique = $this->getPrepareField();

		$string .= $field . ' LIKE :' . $fieldUnique . ' ';

		$this->keyValuePair[':' . $fieldUnique] = sprintf($phraseOrder, $value);

		return $this->where($string);
	}


	/**
	 * @param string $field
	 * @param string $value
	 * @param bool   $positiv
	 * @return PDOQuery|Query
	 */
	public function innerStatement($field, $value, $positiv = true)
	{
		if (!($value instanceof PDOQuery))
		{
			throw new \Exception('InnerStatement muss PDOQuery Objekt sein!');
		}

		return $this->innerStatementEx($field, $value, $positiv);
	}

	/**
	 * @param string                     $field
	 * @param \jamwork\database\PDOQuery $query
	 * @param bool                       $positiv
	 * @return string
	 */
	private function innerStatementEx($field, PDOQuery $query, $positiv = true)
	{
		$innerStmt = $query->get();

		$this->mergeBindValues($query->getBindValues());

		$string = '';

		if (!empty($this->clause) || $this->openClosure)
		{
			$string = $this->concatToClause($this->clause, 'AND', $this->openClosure);
		}

		$string .= $field . ' ' . ($positiv ? 'IN' : 'NOT IN') . ' ' . '(' . $innerStmt . ')' . ' ';

		return $this->where($string);

	}

	/**
	 * Verwenden wenn SQL-Parameter wie Date(), Now() usw. verwendet wird
	 *
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

		$string .= $field . ' ' . $op . ' ' . $value . ' ';

		return $this->where($string);
	}

	/**
	 * @param string           $field
	 * @param string|int|array $value
	 * @param string           $op
	 * @param string           $concat
	 * @return PDOQuery|Query|string
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

			if (is_numeric($value) || is_string($value))
			{
				$fieldUnique = $this->getPrepareField();

				$string .= $field . ' ' . $op . ' :' . $fieldUnique . ' ';

				$this->keyValuePair[':' . $fieldUnique] = $value;
			}
			else
			{
				throw new \InvalidArgumentException('ACHTUNG: Aufruf von addHaving mit unbekannten Parameter! Bitte überprüfen!');
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
     * @param bool   $inOp
     * @return string
     */
	public function in($field, array $values, $inOp = true)
	{
		if (empty($values))
		{
			/** Wenn leeres Array reinkommt, MUSS die Bedingung false sein. Leerer String würde die Bedingung aushebeln: Mardl */
			return '1=2';
		}
		$inStatement = array();

		foreach ($values as $item)
		{
			$fieldUnique = $this->getPrepareField();
			$inStatement[] = ':' . $fieldUnique;
			$this->keyValuePair[':' . $fieldUnique] = $item;
		}

//		$string = $field . ' IN (';
        $string = $field . ($inOp ? ' IN' : ' NOT IN') .'(';
		$string .= implode(',', $inStatement);
		$string .= ')';

		return $string;
	}

	/**
	 * @param string $order
	 * @return PDOQuery|Query
	 */
	public function orderBy($order)
	{
		$this->order = $order;

		return $this;
	}

	/**
	 * @param string $groupby
	 * @return PDOQuery|Query
	 */
	public function groupBy($groupby)
	{
		$this->groupby = $groupby;

		return $this;
	}

	/**
	 * @param int      $offset
	 * @param int|null $limit
	 * @return PDOQuery|Query
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
	 * @return PDOQuery
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
	 * @return PDOQuery
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
	 * @return PDOQuery
	 */
	public function leftJoin($join)
	{
		return $this->join($join, 'LEFT');
	}

	/**
	 * @param string $joinOn
	 * @return PDOQuery|Query
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
	 * @return bool
	 */
	public function isQueryOnce()
	{
		return !empty($this->ownQuery);
	}

	/**
	 * @return Query|string
	 */
	public function get()
	{
		return $this->getSelect();
	}

	/**
	 * @return array
	 */
	public function getBindValues()
	{
		return $this->keyValuePair;
	}

	/**
	 * @param array $keyValuePairs
	 * @return array
	 */
	public function mergeBindValues(array $keyValuePairs)
	{
		$this->keyValuePair = array_merge($this->keyValuePair, $keyValuePairs);
	}

	/**
	 * @return string
	 */
	private function getSelect()
	{
		if ($this->isQueryOnce())
		{
			$query = $this->ownQuery;
			/*
			 * Clearing des onceQuery entfernt -> jedes Objekt hat EINE Aufgabe
			 * verwendet man das Objekt mehrfach für verschidene Queries, so sollte man mehrere Objekte haben!
			 * Zitat: Vadim am 29.02.2012
			 */
			// $this->ownQuery = '';

			$query = trim($query);

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
		if (!empty($this->table))
		{
			$query .= " FROM " . $this->table;
		}
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

		$query = trim($query);

		$this->lastQuery = str_split($query, 255);

		return $query;
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

	/**
	 * @return string
	 */
	public function getPreparedStatement()
	{
		$statement = $this->getSelect();
		foreach ($this->getBindValues() as $key => $value)
		{
			$statement = str_replace($key, "'".$value."'", $statement);
		}

		return $statement;
	}

}