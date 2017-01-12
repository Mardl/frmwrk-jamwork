<?php

namespace jamwork\database;

use jamwork\common\Registry;
use PDO;
use PDOException;

/**
 * Class MysqlDatabase
 *
 * @category Jamwork
 * @package  Jamwork\database
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
class PDODatabase implements Database
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
     * @var string
     */
    protected $appname = '';
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
		$this->dboptions = array_merge(array('port' => '3306', 'driver' => 'mysql', 'charset' => 'UTF8'), $options);
	}

    /**
     * @param string $appname
     * @return void
     */
    public function setAppname($appname) {
        $this->appname = $appname;
    }

	/**
	 * @return void
	 */
	public function openConnection()
	{
		$this->getConnection();
	}

	/**
	 * @throws \Exception
	 * @return PDO
	 */
	public function getConnection()
	{
		if (!$this->connection)
		{

			$connect = $this->dboptions['driver'] . ':host=' . $this->dbhost . ';port=' . $this->dboptions['port'] . ';dbname=' . $this->dbname;

			try
			{
				$options = array(
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
				);
				$this->connection = new PDO($connect, $this->dbuser, $this->dbpwd, $options);
				$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			} catch (PDOException $e)
			{
				throw new \Exception('Open PDO Connection: '.$e->getMessage());
			}
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

			$this->getConnection()->beginTransaction();
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
				$this->getConnection()->commit();
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
			$info = $this->getConnection()->errorInfo();
			$code = $this->getConnection()->errorCode();

			$this->transaction = 0;
			$this->getConnection()->rollBack();

			if ($throwException && $code > 0)
			{
				if (is_array($info))
				{
					$info = var_export($info,true);
				}
				throw new \Exception("DB-Fehler \r\nFehler-Nr: " . $code . "\r\nFehler: " . $info);
			}
		}
	}

	/**
	 * @return PDOQuery|Query
	 */
	public function newQuery()
	{
		$query = new PDOQuery();
		$this->counts['query']++;

		return $query;
	}

	/**
	 * @return PDORecordset|Recordset
	 */
	public function newRecordSet()
	{
		$this->counts['recordset']++;

		return new PDORecordset($this);
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
		$setFieldPDO = '';
		$wherePDO = '';
		$this->readFields($tableName);
		$queryPDO = 'UPDATE ' . $tableName . ' SET ';
		$keyValuePair = array();
		foreach ($this->field[$tableName] as $field => $key)
		{

			if (array_key_exists($field, $recordSet))
			{
				if (!empty($setFieldPDO))
				{
					$setFieldPDO .= ' , ';
				}

				if ($recordSet[$field] === 'NULL' || $recordSet[$field] === null || $this->checkForeignkeyToNull($tableName, $field, $recordSet[$field]))
				{
					$setFieldPDO .= $field . ' = NULL';
				}
				else
				{
					$setFieldPDO .= $field . ' = :' . $field;
					$keyValuePair[':' . $field] = $this->getValue($tableName, $field, $recordSet[$field]);
				}

				if ($key == 'PRI')
				{
					$priCount++;
					if (empty($wherePDO))
					{
						$wherePDO = ' WHERE ';
						$primary = $recordSet[$field];
					}
					else
					{
						// !!! ACHTUNG: bei Multiprimary wird beim update nur der ERSTE zurück geliefert !!!
						$wherePDO .= ' AND ';
					}
					$wherePDO .= $field . ' = :' . $field;
					$keyValuePair[':' . $field] = $recordSet[$field];
				}
			}
		}
		$queryPDO .= $setFieldPDO . $wherePDO;

		if ($priCount > 1)
		{
			//throw new \Exception("Update auf multi Primary key nicht möglich.");
		}

		if (empty($primary))
		{
			throw new \Exception("Kein Primary key angegeben.");
		}


		try
		{
			$stmt = $this->getConnection()->prepare($queryPDO);
			foreach ($keyValuePair as $key => $value)
			{
				$stmt->bindValue($key, $value);
			}
			if ($stmt->execute())
			{
				return $primary;
			}

		} catch (\PDOException $e)
		{
			throw new \Exception('Update PDO Statement: '.$e->getMessage());
//			syslog(LOG_ERR, $e->getMessage());
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
		$keyValuePair = array();
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
					$setField .= $field . ' = :' . $field;
					$keyValuePair[':' . $field] = $recordSet[$field];
				}

			}
		}

		if (empty($setField))
		{
			$setField .= $this->getPrimary($tableName) . ' = null';
		}

		$query .= $setField;


		try
		{
			$stmt = $this->getConnection()->prepare($query);
			foreach ($keyValuePair as $key => $value)
			{
				$stmt->bindValue($key, $value);
			}
			if ($stmt->execute())
			{
				$id = intval($this->getConnection()->lastInsertId());
				$pri = $this->getPrimary($tableName);
				if (isset($recordSet[$pri]) && !empty($recordSet[$pri]))
				{
					$id = $recordSet[$pri];
				}

				return $id;
			}
		} catch (\PDOException $e)
		{
			throw new \Exception('Insert PDO Statement: '.$e->getMessage());
//			syslog(LOG_ERR, $e->getMessage());
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
					//$where = $where . $field . ' = ' . mysql_real_escape_string($recordSet[$field]);
					$where = $where . $field . ' = ' . $this->clear($recordSet[$field]);
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
			$sql = 'DESCRIBE ' . $tableName;
			foreach ($this->getConnection()->query($sql) as $row)
			{
				$this->field[$tableName][$row['Field']] = $row['Key'];
				$this->fieldDescribe[$tableName][$row['Field']] = $row;
			}
		}
	}

	/**
	 * @param string $value
	 * @return float
	 */
	private function getAsDatetime($value)
	{
		$dt = new \DateTime($value);
		if ($dt->format('Y') < 1900)
		{
			$value = NULL;
		}

		return $value;
	}

	/**
	 * @param string $tableName
	 * @param string $field
	 * @return bool
	 */
	private function checkFieldTypeDatetime($tableName, $field)
	{
		$floatingTypes = array(
			'date',
			'time',
			'datetime'
		);
		$fieldType = $this->fieldDescribe[$tableName][$field]['Type'];

		return in_array($fieldType, $floatingTypes);
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

		if ($this->checkFieldTypeDatetime($tableName, $field))
		{
			$valueConverted = $this->getAsDatetime($value);
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
