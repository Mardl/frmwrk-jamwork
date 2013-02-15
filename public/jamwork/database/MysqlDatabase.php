<?php

namespace jamwork\database;

class MysqlDatabase implements Database
{
	protected $dbhost = '';
	protected $dbuser = '';
	protected $dbpwd = '';
	protected $dbname = '';
	protected $connection = false;
	protected $field = array();
	protected $transaction = 0;

	public $counts = array('query' => 0, 'recordset' => 0, 'update' => 0, 'insert' => 0, 'delete' => 0);
	
	private $mockQuery = false;
	private $mockRecordset = false;
	
	public function __construct($host, $user, $pwd, $name )
	{
		$this->dbhost = $host;
		$this->dbuser = $user;
		$this->dbpwd = $pwd;
		$this->dbname = $name;

		$this->connection = mysql_connect($this->dbhost, $this->dbuser, $this->dbpwd) or die ('Error connecting to mysql');

		$db = mysql_select_db($this->dbname) or die ('Error select_db to mysql');
	}

	/**
	 * start Transaction
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
	 * commit Transaction
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
	 * @param bool $noException Exception nicht werfen. Z.B. wenn im catch der rollback gemacht wird
	 * @throws \Exception
	 */
	public function rollback($throwException=true)
	{
		if ($this->transaction > 0)
		{
			$errno = mysql_errno();
			$error = mysql_error();
			$this->transaction = 0;
			$rs = $this->newRecordSet();
			$rs->execute($this->newQuery()->setQueryOnce("ROLLBACK;"));
			$rs->execute($this->newQuery()->setQueryOnce("SET AUTOCOMMIT=1;"));

			if ($throwException)
			{
				throw new \Exception("DB-Fehler\r\nFehler-Nr: ".$errno."\r\nFehler: ".$error);
			}
		}
	}

	/**
	 * @return MysqlQuery|Query
	 */
	public function newQuery()
	{
		$query = new MysqlQuery($this);
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
	
	protected function getPrimary($tableName)
	{
		$this->readFields($tableName);
		foreach ( $this->field[$tableName] as $field => $key)
		{
			if ( $key == 'PRI')
			{
				return $field;
			}
		}
		return '';
	}

	/**
	 * @param string $tableName
	 * @param array $recordSet
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
		$query = 'UPDATE '.$tableName.' SET ';
		foreach ( $this->field[$tableName] as $field => $key) 
		{
			
			if (array_key_exists($field,$recordSet))
			{
				if (!empty($setField))
				{
					$setField .= ', ';
				}
				
				if ($recordSet[$field] !== 'NULL' && $recordSet[$field] !== null)
				{
					$setField .= $field.' = "'.mysql_real_escape_string($recordSet[$field]).'"';
				}
				else 
				{
					$setField .= $field.' = NULL';
				}
				

				if ( $key == 'PRI')	
				{
					$priCount++;
					$where = ' WHERE '.$field.' = '.mysql_real_escape_string($recordSet[$field]);
					$primary = $recordSet[$field];
				}
			}			
		}
		$query .= $setField . $where;
		
		if ($priCount > 1)
		{
			throw new \Exception("Update auf multi Primary key nicht möglich.");
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
	 * @param array $recordSet
	 * @return bool|int
	 */
	public function insert($tableName, array $recordSet)
	{
		$this->counts['insert']++;
		$setField = '';
		$this->readFields($tableName);
		$query = 'INSERT INTO '.$tableName.' SET ';
		
		
		foreach ( $this->field[$tableName] as $field => $key) 
		{
			if (isset($recordSet[$field]))
			{
				if (!empty($setField))
				{
					$setField .= ', ';
				}
				
				if ($recordSet[$field] !== 'NULL')
				{
					$setField .= $field.' = "'.mysql_real_escape_string($recordSet[$field]).'"';
				}
				else
				{
					$setField .= $field.' = NULL';
				}
				
			}
		}

		if (empty($setField))
		{
			$setField .= $this->getPrimary($tableName).' = null';
		}
		
		$query .= $setField;
		
		$queryObj = $this->newQuery()->setQueryOnce($query);
				
		if ($this->newRecordSet()->execute($queryObj)->isSuccessful())
		{
			$id = mysql_insert_id();
			$pri = $this->getPrimary($tableName);
			if(isset($recordSet[$pri]) && !empty($recordSet[$pri]))
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
	 */
	public function clear($str)
	{
		//$str = str_replace('\\', '\\\\', $str);
		//$str = str_replace('"', '\\"', $str);
		return $str;
	}

	/**
	 * @param string $tableName
	 * @param array $recordSet
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
		$query = 'DELETE FROM '.$tableName;
		foreach ( $this->field[$tableName] as $field => $key) 
		{
			if (isset($recordSet[$field]))
			{			
				if ( $key == 'PRI')	
				{
					$priCount++;
					$where = ' WHERE '.$field.' = '.mysql_real_escape_string($recordSet[$field]);
					$primary = $recordSet[$field];
				}
			}
		}
		$query .= $where;
		if ($priCount > 1)
		{
			throw new \Exception("Delete auf multi Primary key nicht möglich.");
		}		
		if (empty($primary))
		{
			throw new \Exception("Kein Primary key angegeben.");
		}
				
		$queryObj = $this->newQuery()->setQueryOnce($query);
		return ($recordSet = $this->newRecordSet()->execute($queryObj)->isSuccessful() ? true : false);
		
	}

	/**
	 * @param string $tableName
	 */
	protected function readFields($tableName)
	{
		if ( !isset($this->field[$tableName]))
		{
			$res = mysql_query('DESCRIBE '.$tableName);
			while($res && $row = mysql_fetch_array($res)) 
			{
    			$this->field[$tableName][$row['Field']] = $row['Key'];
			}
		}
	}
}
