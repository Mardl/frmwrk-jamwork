<?php

namespace jamwork\database;

/**
 * Class MysqlDatabase
 *
 * @category Jamwork
 * @package  jamwork\database
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
class MysqlDatabase implements Database
{

	/**
	 * @var string
	 */
	protected $dbhost = '';
	/**
	 * @var string
	 */
	protected $dbuser = '';
	/**
	 * @var string
	 */
	protected $dbpwd = '';
	/**
	 * @var string
	 */
	protected $dbname = '';
	/**
	 * @var array
	 */
	protected $dboptions = array();
	/**
	 * @var bool
	 */
	protected $connection = false;
	/**
	 * @var array
	 */
	protected $field = array();
	/**
	 * @var array
	 */
	protected $fieldDescribe = array();
	/**
	 * @var int
	 */
	protected $transaction = 0;

	/**
	 * @var array
	 */
	public $counts = array('query' => 0, 'recordset' => 0, 'update' => 0, 'insert' => 0, 'delete' => 0);

	/**
	 * @param string $host
	 * @param string $user
	 * @param string $pwd
	 * @param string $name
	 * @param array  $options
	 */
	public function __construct($host, $user, $pwd, $name, $options = array())
	{
		$this->dbhost = $host;
		$this->dbuser = $user;
		$this->dbpwd = $pwd;
		$this->dbname = $name;
		$this->dboptions = $options;

		$this->getConnection();
	}

	/**
	 * @return mixed
	 */
	public function getConnection()
	{
		if (!$this->connection)
		{
			$this->connection = mysql_connect($this->dbhost, $this->dbuser, $this->dbpwd) or die ('Error connecting to mysql');

			$db = mysql_select_db($this->dbname) or die ('Error select_db to mysql');
		}

		return $this->connection;
	}

	/**
	 * @return void
	 */
	public function startTransaction()
	{

		if ($this->transaction <= 0)
		{
			$this->transaction = 0; // initialisieren, sicher ist sicher;
			$rs = $this->newRecordSet();
			$rs->execute($this->newQuery()->setQueryOnce("SET AUTOCOMMIT=0;"));
			$rs->execute($this->newQuery()->setQueryOnce("START TRANSACTION;"));
		}

		$this->transaction++;
	}

	/**
	 * @return void
	 */
	public function commit()
	{
		if ($this->transaction > 0)
		{
			$this->transaction--;
			if ($this->transaction == 0)
			{
				$rs = $this->newRecordSet();
				$rs->execute($this->newQuery()->setQueryOnce("COMMIT;"));
				$rs->execute($this->newQuery()->setQueryOnce("SET AUTOCOMMIT=1;"));
			}
		}
	}

	/**
	 * @param bool $throwException Exception nicht werfen. Z.B. wenn im catch der rollback gemacht wird
	 * @return void
	 * @throws \Exception
	 */
	public function rollback($throwException = true)
	{
		if ($this->transaction > 0)
		{
			$errno = mysql_errno();
			$error = mysql_error();
			$this->transaction = 0;
			$rs = $this->newRecordSet();
			$rs->execute($this->newQuery()->setQueryOnce("ROLLBACK;"));
			$rs->execute($this->newQuery()->setQueryOnce("SET AUTOCOMMIT=1;"));

			if ($throwException && $error > 0)
			{
				throw new \Exception("DB-Fehler\r\nFehler-Nr: " . $errno . "\r\nFehler: " . $error);
			}
		}
	}

	/**
	 * @return MysqlQuery|Query
	 */
	public function newQuery()
	{
		$query = new MysqlQuery();
		$this->counts['query']++;

		return $query;
	}

	/**
	 * @return MysqlRecordset|Recordset
	 */
	public function newRecordSet()
	{
		$this->counts['recordset']++;

		return new MysqlRecordset();
	}

	/**
	 * @param string $tableName
	 * @return int|string
	 */
	protected function getPrimary($tableName)
	{
		$this->readFields($tableName);
		foreach ($this->field[$tableName] as $field => $key)
		{
			if ($key == 'PRI')
			{
				return $field;
			}
		}

		return '';
	}

	/**
	 * @param string $tableName
	 * @param array  $recordSet
	 * @return bool|int
	 * @throws \Exception
	 */
	public function update($tableName, array $recordSet)
	{
		$this->counts['update']++;
		$primary = 0;
		$priCount = 0;
		$setField = '';
		$where = '';
		$this->readFields($tableName);
		$query = 'UPDATE ' . $tableName . ' SET ';
		foreach ($this->field[$tableName] as $field => $key)
		{

			if (array_key_exists($field, $recordSet))
			{
				if (!empty($setField))
				{
					$setField .= ', ';
				}

				if ($recordSet[$field] === 'NULL' || $recordSet[$field] === null || $this->checkForeignkeyToNull($tableName, $field, $recordSet[$field]))
				{
					$setField .= $field . ' = NULL';
				}
				else
				{
					$setField .= $field . ' = "' . mysql_real_escape_string($this->getValue($tableName, $field, $recordSet[$field])) . '"';
				}

				if ($key == 'PRI')
				{
					$priCount++;
					if (empty($where))
					{
						$where = ' WHERE ';
						$primary = $recordSet[$field];
					}
					else
					{
						// !!! ACHTUNG: bei Multiprimary wird beim update nur der ERSTE zurück geliefert !!!
						$where .= ' AND ';
					}
					$where .= $field . ' = ' . mysql_real_escape_string($recordSet[$field]);
				}
			}
		}
		$query .= $setField . $where;

		if ($priCount > 1)
		{
			//throw new \Exception("Update auf multi Primary key nicht möglich.");
		}

		if (empty($primary))
		{
			throw new \Exception("Kein Primary key angegeben.");
		}

		$queryObj = $this->newQuery()->setQueryOnce($query);
		if ($recordSet = $this->newRecordSet()->execute($queryObj)->isSuccessful())
		{
			return $primary;
		}

		return false;

	}

	/**
	 * @param string $tableName
	 * @param string $field
	 * @param mixed  $value
	 * @return bool
	 */
	private function checkForeignkeyToNull($tableName, $field, $value)
	{
		if (is_string($value))
		{
			$value = trim($value);
		}
		$isValueNull = $value === 'NULL' || $value === '0' || empty($value);
		$isForeignkey = $this->field[$tableName][$field] == 'MUL';
		$nullAllowed = $this->fieldDescribe[$tableName][$field]['Null'] == 'YES';

		return $isForeignkey && $isValueNull && $nullAllowed;
	}

	/**
	 * @param string $tableName
	 * @param string $field
	 * @param string $typeToCompare
	 * @return bool
	 */
	private function checkFieldTypeFloat($tableName, $field)
	{
		$floatingTypes = array(
			'float',
			'double',
			'decimal'
		);
		$fieldType = $this->fieldDescribe[$tableName][$field]['Type'];

		return in_array($fieldType, $floatingTypes);
	}

	/**
	 * @param string $tableName
	 * @param array  $recordSet
	 * @return bool|int
	 */
	public function insert($tableName, array $recordSet)
	{
		$this->counts['insert']++;
		$setField = '';
		$this->readFields($tableName);
		$query = 'INSERT INTO ' . $tableName . ' SET ';


		foreach ($this->field[$tableName] as $field => $key)
		{
			if (isset($recordSet[$field]))
			{
				if (!empty($setField))
				{
					$setField .= ', ';
				}

				if ($recordSet[$field] === 'NULL' || $recordSet[$field] === null || $this->checkForeignkeyToNull($tableName, $field, $recordSet[$field]))
				{
					$setField .= $field . ' = NULL';
				}
				else
				{
					$setField .= $field . ' = "' . mysql_real_escape_string($this->getValue($tableName, $field, $recordSet[$field])) . '"';
				}

			}
		}

		if (empty($setField))
		{
			$setField .= $this->getPrimary($tableName) . ' = null';
		}

		$query .= $setField;

		$queryObj = $this->newQuery()->setQueryOnce($query);

		$execRs = $this->newRecordSet()->execute($queryObj);

		if ($execRs->isSuccessful())
		{
			$id = mysql_insert_id();
			$pri = $this->getPrimary($tableName);
			if (isset($recordSet[$pri]) && !empty($recordSet[$pri]))
			{
				$id = $recordSet[$pri];
			}

			return $id;
		}

		return false;
	}

	/**
	 * @param string $str
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function clear($str)
	{
		// funcktion wurde injektion sicher abgelöst mit mysql_real_escape_string
		//$str = str_replace('\\', '\\\\', $str);
		//$str = str_replace('"', '\\"', $str);
		return $str;
	}

	/**
	 * @param string $tableName
	 * @param array  $recordSet
	 * @return bool
	 * @throws \Exception
	 */
	public function delete($tableName, array $recordSet)
	{
		$this->counts['delete']++;
		$primary = 0;
		$priCount = 0;
		$where = '';
		$this->readFields($tableName);
		$query = 'DELETE FROM ' . $tableName;
		$where = '';

		foreach ($this->field[$tableName] as $field => $key)
		{
			if (isset($recordSet[$field]))
			{
				if ($key == 'PRI')
				{
					if (empty($where))
					{
						$where = ' WHERE ';
					}
					else
					{
						$where .= ' AND ';
					}

					$priCount++;
					$where = $where . $field . ' = ' . mysql_real_escape_string($recordSet[$field]);
					$primary = $recordSet[$field];
				}
			}
		}
		$query .= $where;

		if (empty($primary))
		{
			throw new \Exception("Kein Primary key angegeben.");
		}

		$queryObj = $this->newQuery()->setQueryOnce($query);

		return ($recordSet = $this->newRecordSet()->execute($queryObj)->isSuccessful() ? true : false);

	}

	/**
	 * @param string $tableName
	 * @return void
	 */
	protected function readFields($tableName)
	{
		if (!isset($this->field[$tableName]))
		{
			$res = mysql_query('DESCRIBE ' . $tableName);
			while ($res && $row = mysql_fetch_array($res))
			{
				$this->field[$tableName][$row['Field']] = $row['Key'];
				$this->fieldDescribe[$tableName][$row['Field']] = $row;
			}
		}
	}

	/**
	 * @param $tableName
	 * @param $field
	 * @param $value
	 * @return bool|float
	 */
	private function getValue($tableName, $field, $value)
	{
		$valueConverted = $value;
		if ($this->checkFieldTypeFloat($tableName, $field))
		{
			$valueConverted = $this->getAsFloat($value);
		}

		// wenn nicht gewandelt werden konnte dann den original Wert verwenden
		if (!$valueConverted)
		{
			$valueConverted = $value;
		}

		return $valueConverted;
	}

	/**
	 * @param string $value
	 * @return float
	 */
	private function getAsFloat($value)
	{
		preg_match('/([-0-9\.\,]+)/', $value, $result);

		if (!isset($result[0]))
		{
			return $value;
		}

		$value = $result[0];

		$lastposPoint = strrpos($value, '.');
		$lastposComma = strrpos($value, ',');

		if ($lastposPoint === false && $lastposComma === false)
		{
			return $value;
		}

		if ($lastposComma > $lastposPoint || $lastposPoint === false)
		{
			$lastPos = $lastposComma;
		}
		else
		{
			$lastPos = $lastposPoint;
		}

		$value = substr_replace($value, '####', $lastPos, 0);

		$value = str_replace(',', '', $value);
		$value = str_replace('.', '', $value);

		$value = str_replace('####', '.', $value);

		return (float)$value;
	}

	/**
	 * @param $statement
	 * @return bool
	 * @throws \Exception
	 */
	public function execStoreProc($statement)
	{
		throw new \Exception('Funktion nicht implementiert');
	}
}
