<?php

namespace jamwork\database;

use jamwork\common\Registry;
use PDO;
use PDOException;

/**
 * Class MssqlDatabase
 *
 * @category Jamwork
 * @package  Jamwork\database
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
class MssqlDatabase implements Database
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
     * @param null   $appname
     */
	public function __construct($host, $user, $pwd, $name, $options = array(), $appname = NULL)
	{
		$this->dbhost = $host;
		$this->dbuser = $user;
		$this->dbpwd = $pwd;
		$this->dbname = $name;
        $this->appname = $appname;
		$this->dboptions = array_merge(array('port' => '1433', 'driver' => 'dblib'), $options);
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
			if(stristr(PHP_OS, 'WIN') && PHP_OS != 'Darwin')
			{
				$connect = $this->dboptions['driver'] . ':server=' . $this->dbhost . ',' . $this->dboptions['port'] . ';Database=' . $this->dbname;
			}
			else
			{
				$connect = $this->dboptions['driver'] . ':host=' . $this->dbhost . ';port=' . $this->dboptions['port'] . ';dbname=' . $this->dbname;
			}
            if (!empty($this->appname))
            {
                $connect .= ';appname='.$this->appname;
            }


			try
			{
				$options = array();
				/*
				$options = array(
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
				);
				*/
				$this->connection = new PDO($connect, $this->dbuser, $this->dbpwd, $options);
				$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
				$this->connection->query("SET ANSI_NULLS, ANSI_NULL_DFLT_ON, QUOTED_IDENTIFIER, CONCAT_NULL_YIELDS_NULL, ANSI_WARNINGS, ANSI_PADDING ON");

			} catch (PDOException $e)
			{
				throw new \Exception('Open Mssql Connection: '.$e->getMessage());
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
	 * @return MysqlQuery|Query
	 */
	public function newQuery()
	{
		$query = new MssqlQuery();
		$this->counts['query']++;

		return $query;
	}

	/**
	 * @return MysqlRecordset|Recordset
	 */
	public function newRecordSet()
	{
		$this->counts['recordset']++;

		return new MssqlRecordset($this);
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
				else {
					if ($this->checkForeignkeyToNull($tableName, $field, $recordSet[$field]) || ($this->isNullAllowed($tableName, $field) && ($recordSet[$field] === 'NULL' || $recordSet[$field] === null)) )
					{
						$setFieldPDO .= $field . ' = NULL';
					}
					else
					{
						if ($this->getPrimary($tableName) != $field)
						{
							$setFieldPDO .= $field . ' = :' . $field;
							$keyValuePair[':' . $field] = $this->getValue($tableName, $field, $recordSet[$field]);
						}
					}
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
				//$check = !empty($value) ? iconv('UTF-8', 'ISO8859-1', $value) : $value;
				$stmt->bindValue($key, $value);
			}
			if ($stmt->execute())
			{
				return $primary;
			}

		} catch (\PDOException $e)
		{
			throw new \Exception('Update Mssql Statement: '.$e->getMessage());
//			syslog(LOG_ERR, $e->getMessage());
		}

		return false;
	}

	private function isNullAllowed($tableName, $field)
	{
		if (!isset($this->fieldDescribe[$tableName][$field]))
		{
			return false;
		}
		return $this->fieldDescribe[$tableName][$field]['IS_NULLABLE'] == 'YES';
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
		$nullAllowed = $this->isNullAllowed($tableName, $field);

		return $isForeignkey && $isValueNull && $nullAllowed;
	}

	/**
	 * @param string $tableName
	 * @param string $field
	 * @return bool
	 */
	private function checkFieldTypeFloat($tableName, $field)
	{
		$floatingTypes = array(
			'float',
			'double',
			'decimal'
		);
		$fieldType = $this->fieldDescribe[$tableName][$field]['TYPE_NAME'];

		return in_array($fieldType, $floatingTypes);
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
		$fieldType = $this->fieldDescribe[$tableName][$field]['TYPE_NAME'];

		return in_array($fieldType, $floatingTypes);
	}

	/**
	 * @param string $tableName
	 * @param array  $recordSet
	 * @return bool|int
	 * @throws \Exception
	 */
	public function insert($tableName, array $recordSet)
	{
		$this->counts['insert']++;
		$setField = '';
		$setValue = '';
		$keyValuePair = array();
		$this->readFields($tableName);
		$query = 'INSERT INTO ' . $tableName ;

		foreach ($this->field[$tableName] as $field => $key)
		{
			if (array_key_exists($field, $recordSet))
			{
				if (!empty($setField))
				{
					$setField .= ', ';
					$setValue .= ', ';
				}

				if ($this->checkForeignkeyToNull($tableName, $field, $recordSet[$field]) || ($this->isNullAllowed($tableName, $field) && ($recordSet[$field] === 'NULL' || $recordSet[$field] === null)) )
				{
					$setField .= $field;
					$setValue .= 'NULL';
				}
				else
				{
					if ($this->getPrimary($tableName) != $field)
					{
						$setField .= $field;
						$setValue .= ':' . $field;
						$keyValuePair[':' . $field] = $this->getValue($tableName, $field, $recordSet[$field]);
					}
				}

			}
		}

		if (empty($setField))
		{
			throw new \Exception('Insert Mssql Statement Tabelle nicht vorhanden: '.$tableName);
			/*
			$setField .= $this->getPrimary($tableName);
			$setValue .= 'NULL';
			*/
		}

		$query .= ' ('.$setField.') VALUES ('.$setValue.')';


		try
		{
			$stmt = $this->getConnection()->prepare($query);

			foreach ($keyValuePair as $key => $value)
			{
				//$check = !empty($value) ? iconv('UTF-8', 'ISO8859-1', $value) : $value;
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
			throw new \Exception('Insert Mssql Statement: '.$e->getMessage());
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
			$foreignKeys = array();
			$sqlKeysH = 'sp_helpconstraint "'.$tableName.'", "nomsg"';
			foreach ($this->getConnection()->query($sqlKeysH) as $row)
			{
				if ($row['constraint_type'] == 'FOREIGN KEY')
				{
					$foreignKeys[$row['constraint_keys']] = 'MUL';
				}
			}

			$tableNameArr = explode('.', $tableName);
			$onlyTableName = count($tableNameArr) > 0 ? $tableNameArr[1] : $tableNameArr[0];
			$schema = count($tableNameArr) > 0 ? $tableNameArr[0] : '';

			$sqlKeys = 'sp_pkeys ' . $onlyTableName .', '.$schema;
			$keyRowsStmt = $this->getConnection()->query($sqlKeys);
			$keyRows = $keyRowsStmt->fetch();
			$sql = 'sp_columns ' . $onlyTableName .', '.$schema;
			foreach ($this->getConnection()->query($sql) as $row)
			{
				$this->field[$tableName][$row['COLUMN_NAME']] = $row['COLUMN_NAME'] == $keyRows['COLUMN_NAME']?  'PRI' : (isset($foreignKeys[$row['COLUMN_NAME']]) ? 'MUL' : '');
				$this->fieldDescribe[$tableName][$row['COLUMN_NAME']] = $row;
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
		/*
		 * Aufbau einer Store Procedure für SQL Server
		CREATE PROCEDURE dsp_test_exception @_Param VARCHAR(50), @_IntVal INTEGER
		AS
		BEGIN
			DECLARE @_tmp INTEGER = 55

			BEGIN TRY

				RAISERROR('Irgendwas ist schief gegangen', 11, 125)

				select @_tmp = 55

			END TRY
			BEGIN CATCH
				DECLARE @ErrorMessage NVARCHAR(4000)
				DECLARE @ErrorSeverity INTEGER
				DECLARE @ErrorState INTEGER

				SELECT @ErrorMessage = ERROR_MESSAGE(), @ErrorSeverity = ERROR_SEVERITY(), @ErrorState = ERROR_STATE()
				RAISERROR (@ErrorMessage, @ErrorSeverity, @ErrorState)

				select ERROR_MESSAGE() as 'message', ERROR_STATE() as 'code'

			END CATCH

		END
		*/
		try
		{
			$result = $this->getConnection()->query('EXEC '.$statement);

			$errorInfo = $result->errorInfo();

			if (isset($errorInfo[4]) && ! empty($errorInfo[4]))
			{
				$allData = $result->fetchAll();
				throw new \PDOException($errorInfo[2].' '.$allData[0]['message'],$allData[0]['code']);
			}

		} catch (\PDOException  $e)
		{
			throw new \Exception($e->getMessage().' Code:'.$e->getCode());
		}
		return true;
	}
}
