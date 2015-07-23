<?php


namespace unittest\jamwork\database;

use jamwork\database\MysqlDatabase;
use jamwork\database\MysqlRecordset;
use jamwork\database\MysqlQuery;
use jamwork\common\Registry;

/**
 * Class MysqlDatabaseTest
 *
 * @category Jamwork
 * @package  unittest\jamwork\database
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
class MysqlDatabaseTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var \jamwork\database\MysqlQuery
	 */
	private $query;
	/**
	 * @var \jamwork\database\MysqlRecordset
	 */
	private $recordset;

	/**
	 * @var \jamwork\database\MysqlDatabase
	 */
	public $mysqlDatabase = null;

	/**
	 * @return void
	 */
	public function testNewRecordSet()
	{
		$recordset = $this->mysqlDatabase->newRecordSet();
		$this->assertInstanceOf('jamwork\database\MysqlRecordset', $recordset);
	}

	/**
	 * @return void
	 */
	public function testNewQuery()
	{
		$query = $this->mysqlDatabase->newQuery();
		$this->assertInstanceOf('jamwork\database\MysqlQuery', $query);
	}

	/**
	 * @return void
	 */
	public function testInsert()
	{
		$table = 'testtable';
		$array = array('tst_id' => '1', 'tst_name' => 'Tester5', 'tst_datum' => '0000-00-00 00:00:00');
		$newId = $this->mysqlDatabase->insert($table, $array);

		$this->assertTrue($newId == 1);

		$this->query->from($table)->where('tst_id = ' . $newId);
		$compareArray = $this->recordset->execute($this->query)->get();
		$this->assertSame($array, $compareArray);
	}

	/**
	 * @return void
	 */
	public function testInsert_null()
	{
		$table = 'testtable3';
		$array = array('tst_id' => '88', 'tst_id2' => '66', 'tst_id3' => 'NULL', 'tst_id4' => 'NULL');
		$newId = $this->mysqlDatabase->insert($table, $array);

		$this->assertTrue($newId == 88);

		$this->query->from($table)->where('tst_id = ' . $newId);
		$compareArray = $this->recordset->execute($this->query)->get();
		$this->assertSame(array('tst_id' => '88', 'tst_id2' => '66', 'tst_id3' => null, 'tst_id4' => null), $compareArray);
	}

	/**
	 * @return void
	 */
	public function testInsert_nullNotAllowd()
	{
		$table = 'testtable2';
		$array = array('tst_id' => '88', 'tst_id2' => 'NULL', 'tst_id3' => 'NULL');
		$newId = $this->mysqlDatabase->insert($table, $array);

		$this->assertTrue($newId === 0);
	}

	/**
	 * @return void
	 */
	public function testInsert_ForeignToNULL()
	{
		$table = 'testtable3';
		$array = array('tst_id' => '88', 'tst_id4' => '0');
		$newId = $this->mysqlDatabase->insert($table, $array);

		$this->assertTrue($newId == 88);

		$this->query->from($table)->where('tst_id = ' . $newId);
		$compareArray = $this->recordset->execute($this->query)->get();
		$this->assertSame(array('tst_id' => '88', 'tst_id2' => '0', 'tst_id3' => null, 'tst_id4' => null), $compareArray);

	}

	/**
	 * @return void
	 */
	public function testInsert_ForeignToNULLnumber()
	{
		$table = 'testtable3';
		$array = array('tst_id' => '88', 'tst_id4' => 0);
		$newId = $this->mysqlDatabase->insert($table, $array);

		$this->assertTrue($newId == 88);

		$this->query->from($table)->where('tst_id = ' . $newId);
		$compareArray = $this->recordset->execute($this->query)->get();
		$this->assertSame(array('tst_id' => '88', 'tst_id2' => '0', 'tst_id3' => null, 'tst_id4' => null), $compareArray);

	}

	/**
	 * @return void
	 */
	public function testInsert_ForeignToNULLEmptyString()
	{
		$table = 'testtable3';
		$array = array('tst_id' => '88', 'tst_id4' => '    ');
		$newId = $this->mysqlDatabase->insert($table, $array);

		$this->assertTrue($newId == 88);

		$this->query->from($table)->where('tst_id = ' . $newId);
		$compareArray = $this->recordset->execute($this->query)->get();
		$this->assertSame(array('tst_id' => '88', 'tst_id2' => '0', 'tst_id3' => null, 'tst_id4' => null), $compareArray);

	}

	/**
	 * @return void
	 */
	public function testInsert_ForeignToSuccess()
	{
		$table = 'testtable';
		$array = array('tst_id' => '123');
		$newId = $this->mysqlDatabase->insert($table, $array);
		$this->assertTrue($newId == 123);

		$table = 'testtable3';
		$array = array('tst_id' => '88', 'tst_id4' => '123');
		$newId = $this->mysqlDatabase->insert($table, $array);

		$this->assertTrue($newId == 88);

		$this->query->from($table)->where('tst_id = ' . $newId);
		$compareArray = $this->recordset->execute($this->query)->get();
		$this->assertSame(array('tst_id' => '88', 'tst_id2' => '0', 'tst_id3' => null, 'tst_id4' => '123'), $compareArray);

	}

	/**
	 * @return void
	 */
	public function testInsert_ForeignToSuccessAsNumber()
	{
		$table = 'testtable';
		$array = array('tst_id' => '123');
		$newId = $this->mysqlDatabase->insert($table, $array);
		$this->assertTrue($newId == 123);

		$table = 'testtable3';
		$array = array('tst_id' => '88', 'tst_id4' => 123);
		$newId = $this->mysqlDatabase->insert($table, $array);

		$this->assertTrue($newId == 88);

		$this->query->from($table)->where('tst_id = ' . $newId);
		$compareArray = $this->recordset->execute($this->query)->get();
		$this->assertSame(array('tst_id' => '88', 'tst_id2' => '0', 'tst_id3' => null, 'tst_id4' => '123'), $compareArray);

	}

	/**
	 * @return void
	 */
	public function testInsert_ForeignToNULLFailed()
	{
		$table = 'testtable3';
		$array = array('tst_id' => '88', 'tst_id4' => 55);
		$newId = $this->mysqlDatabase->insert($table, $array);

		$this->assertFalse($newId);

	}

	/**
	 * @return void
	 */
	public function testInsert_insertEmpty()
	{
		$table = 'testtable';
		$newId = $this->mysqlDatabase->insert($table, array());

		$this->assertTrue($newId !== false);
	}

	/**
	 * @return void
	 */
	public function testInsert_Minimum()
	{
		$table = 'testtable';
		$array = array('tst_name' => 'Tester5');
		$newId = $this->mysqlDatabase->insert($table, $array);


		$this->query->from($table)->where('tst_id = ' . $newId);
		$compareArray = $this->recordset->execute($this->query)->get();
		$this->assertTrue(is_array($compareArray));

	}

	/**
	 * @return void
	 */
	public function testUpdate()
	{
		$table = 'testtable';
		$array = array('tst_name' => 'testUpdate');
		$newId = $this->mysqlDatabase->insert($table, $array);

		$table = 'testtable';
		$this->query->from($table)->where('tst_id = ' . $newId);
		$array = $this->recordset->execute($this->query)->get();

		$array['tst_name'] = 'Tester2';
		$retId = $this->mysqlDatabase->update($table, $array);

		$this->assertTrue($retId == $newId);

		$this->query->from($table)->where('tst_id = ' . $newId);
		$compareArray = $this->recordset->execute($this->query)->get();
		$this->assertSame($array, $compareArray);

	}

	/**
	 * @return void
	 */
	public function testUpdate_ForeignToSuccessAsNumber()
	{
		$table = 'testtable';
		$array = array('tst_id' => '123');
		$newId = $this->mysqlDatabase->insert($table, $array);
		$this->assertTrue($newId == 123);

		$table = 'testtable3';
		$array = array('tst_id' => '88', 'tst_id2' => '1', 'tst_id4' => '');
		$newId = $this->mysqlDatabase->insert($table, $array);

		$this->assertTrue($newId == 88);

		$table = 'testtable3';
		$this->query->from($table)->where('tst_id = 88');
		$array = $this->recordset->execute($this->query)->get();
		$this->assertSame(array('tst_id' => '88', 'tst_id2' => '1', 'tst_id3' => null, 'tst_id4' => null), $array);

		$array['tst_id4'] = '123';
		$retId = $this->mysqlDatabase->update($table, $array);

		$this->query->from($table)->where('tst_id = 88');
		$compareArray = $this->recordset->execute($this->query)->get();
		$this->assertSame(array('tst_id' => '88', 'tst_id2' => '1', 'tst_id3' => null, 'tst_id4' => '123'), $compareArray);

	}

	/**
	 * @return void
	 */
	public function testUpdate_ForeignToSuccessEmpty()
	{
		$table = 'testtable';
		$array = array('tst_id' => '123');
		$newId = $this->mysqlDatabase->insert($table, $array);
		$this->assertTrue($newId == 123);

		$table = 'testtable3';
		$array = array('tst_id' => '88', 'tst_id2' => '1', 'tst_id4' => '');
		$newId = $this->mysqlDatabase->insert($table, $array);

		$this->assertTrue($newId == 88);

		$table = 'testtable3';
		$this->query->from($table)->where('tst_id = 88');
		$array = $this->recordset->execute($this->query)->get();
		$this->assertSame(array('tst_id' => '88', 'tst_id2' => '1', 'tst_id3' => null, 'tst_id4' => null), $array);

		$array['tst_id4'] = '     ';
		$retId = $this->mysqlDatabase->update($table, $array);

		// MultiPrimaryKey liefert nur den ersten PrimaryKey zurück!
		$this->assertTrue($retId == 88);

		$this->query->from($table)->where('tst_id = 88');
		$compareArray = $this->recordset->execute($this->query)->get();
		$this->assertSame(array('tst_id' => '88', 'tst_id2' => '1', 'tst_id3' => null, 'tst_id4' => null), $compareArray);

	}

	/**
	 * @return void
	 */
	public function testUpdate_MoreFields()
	{
		$table = 'testtable';
		$array = array('tst_name' => 'MoreFields');
		$newId = $this->mysqlDatabase->insert($table, $array);

		$array['tst_id'] = $newId;
		$array['tst_name'] = 'Tester2';
		$array['kun_name'] = 'Kundenname';
		$retId = $this->mysqlDatabase->update($table, $array);
		$this->assertTrue($retId == $newId);

		$this->query->from($table);
		$compareArray = $this->recordset->execute($this->query)->get();
		$this->assertSame($array['tst_name'], $compareArray['tst_name']);

	}

	/**
	 * @return void
	 */
	public function testUpdate_OhnePrimary()
	{
		$table = 'testtable';
		$array = array('tst_name' => 'MoreFields');
		$newId = $this->mysqlDatabase->insert($table, $array);

		$array['tst_name'] = 'Tester2';
		try
		{
			$retId = $this->mysqlDatabase->update($table, $array);
		} catch (\Exception $expected)
		{
			return;
		}

		$this->fail('An expected Exception has not been raised.');
	}

	/**
	 * @return void
	 */
	public function testUpdate_null()
	{
		$table = 'testtable3';
		$array = array('tst_id' => '77', 'tst_id3' => '55');
		$newId = $this->mysqlDatabase->insert($table, $array);

		$array['tst_id'] = $newId;
		$array['tst_id3'] = 'NULL';
		$retId = $this->mysqlDatabase->update($table, $array);
		$this->assertTrue($retId == $newId);

		$this->query->from($table);
		$compareArray = $this->recordset->execute($this->query)->get();
		$this->assertSame(null, $compareArray['tst_id3']);
	}

	/**
	 * @return void
	 */
	public function testDelete()
	{

		$table = 'testtable';
		$array = array('tst_name' => 'Delete');
		$newId = $this->mysqlDatabase->insert($table, $array);

		$array['tst_id'] = $newId;
		$ret = $this->mysqlDatabase->delete($table, $array);
		$this->assertTrue($ret);

		$this->query->from($table)->where('tst_id = ' . $newId);
		$array = $this->recordset->execute($this->query)->get();
		$this->assertFalse($array);

	}

	/**
	 * @return void
	 */
	public function testDelete_TwoPrimary()
	{

		$table = 'testtable3';

		$array['tst_id'] = 1;
		$array['tst_id2'] = 2;
		$ret = $this->mysqlDatabase->delete($table, $array);
		$this->assertTrue($ret);
	}

	/**
	 * @return void
	 */
	public function testDelete_OhnePrimary()
	{

		$table = 'testtable';
		$array = array('tst_name' => 'Delete');
		$newId = $this->mysqlDatabase->insert($table, $array);

		try
		{
			$ret = $this->mysqlDatabase->delete($table, $array);
		} catch (\Exception $expected)
		{
			return;
		}
		$this->fail('An expected Exception has not been raised.');
	}

	/**
	 * @return void
	 */
	public function testReadFields()
	{
		$fields = $this->readAttribute($this->mysqlDatabase, 'field');
		$this->assertEmpty($fields);

		$method = new \ReflectionMethod($this->mysqlDatabase, 'readFields');
		$method->setAccessible(true);
		$method->invoke($this->mysqlDatabase, 'testtable');

		$fields = $this->readAttribute($this->mysqlDatabase, 'field');
		$this->assertTrue(count($fields) > 0);
	}

	/**
	 * @return void
	 */
	public function testGetPrimary_Mit()
	{
		$method = new \ReflectionMethod($this->mysqlDatabase, 'getPrimary');
		$method->setAccessible(true);
		$field = $method->invoke($this->mysqlDatabase, 'testtable');

		$this->assertSame($field, 'tst_id');
	}

	/**
	 * @return void
	 */
	public function testGetPrimary_Ohne()
	{
		$method = new \ReflectionMethod($this->mysqlDatabase, 'getPrimary');
		$method->setAccessible(true);
		$field = $method->invoke($this->mysqlDatabase, 'testtable2');

		$this->assertSame($field, '');
	}

	/**
	 * @return void
	 */
	public function testStartTransaction()
	{
		$transaction = $this->readAttribute($this->mysqlDatabase, 'transaction');
		$this->assertSame($transaction, 0);

		$this->mysqlDatabase->startTransaction();

		$transaction = $this->readAttribute($this->mysqlDatabase, 'transaction');
		$this->assertSame($transaction, 1);
	}

	/**
	 * @return void
	 */
	public function testCommit_counter()
	{
		$transaction = $this->readAttribute($this->mysqlDatabase, 'transaction');
		$this->assertSame($transaction, 0);

		$this->mysqlDatabase->commit();

		$transaction = $this->readAttribute($this->mysqlDatabase, 'transaction');
		$this->assertSame($transaction, 0);

		$this->mysqlDatabase->startTransaction();

		$transaction = $this->readAttribute($this->mysqlDatabase, 'transaction');
		$this->assertSame($transaction, 1);

		$this->mysqlDatabase->commit();

		$transaction = $this->readAttribute($this->mysqlDatabase, 'transaction');
		$this->assertSame($transaction, 0);
	}

	/**
	 * @return void
	 */
	public function testRollback_counter()
	{
		$transaction = $this->readAttribute($this->mysqlDatabase, 'transaction');
		$this->assertSame($transaction, 0);

		$this->mysqlDatabase->rollback();

		$transaction = $this->readAttribute($this->mysqlDatabase, 'transaction');
		$this->assertSame($transaction, 0);

		$this->mysqlDatabase->startTransaction();

		$transaction = $this->readAttribute($this->mysqlDatabase, 'transaction');
		$this->assertSame($transaction, 1);

		$this->mysqlDatabase->rollback(false);

		$transaction = $this->readAttribute($this->mysqlDatabase, 'transaction');
		$this->assertSame($transaction, 0);
	}

	/**
	 * @return void
	 */
	public function testRollback_Exception()
	{
		$this->mysqlDatabase->startTransaction();

		$transaction = $this->readAttribute($this->mysqlDatabase, 'transaction');
		$this->assertSame($transaction, 1);

		try
		{
			$query = $this->mysqlDatabase->newQuery();
			$query->setQueryOnce("SELECT tst_foobar FROM testtable WHERE tst_id = 4");
			$this->recordset->execute($query);
			$this->mysqlDatabase->rollback();
		} catch (\Exception $e)
		{
			return;
		}

		$this->fail('An expected Exception has not been raised.');
	}

	/**
	 * @return void
	 */
	protected function setUp()
	{
		$this->mysqlDatabase = new MysqlDatabase('localhost', 'test_jamwork', 'test_jamwork', 'test_jamwork');
		$registry = Registry::getInstance();
		$registry->setDatabase($this->mysqlDatabase);
		$this->query = $this->mysqlDatabase->newQuery();
		$this->recordset = $this->mysqlDatabase->newRecordSet();

		$query = $this->mysqlDatabase->newQuery();
		$query->setQueryOnce("DROP TABLE IF EXISTS testtable");
		$this->recordset->execute($query);
		$query->setQueryOnce("DROP TABLE IF EXISTS testtable2");
		$this->recordset->execute($query);
		$query->setQueryOnce("DROP TABLE IF EXISTS testtable3");
		$this->recordset->execute($query);

		$query = $this->mysqlDatabase->newQuery();
		$query->setQueryOnce("CREATE TABLE IF NOT EXISTS testtable (
  tst_id int(11) NOT NULL AUTO_INCREMENT,
  tst_name varchar(100) NOT NULL,
  tst_datum datetime NOT NULL,
  PRIMARY KEY (tst_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
		$this->recordset->execute($query);

		$query->setQueryOnce("CREATE TABLE IF NOT EXISTS testtable2 (
  tst_id int(11) NOT NULL,
  tst_name varchar(100) NOT NULL,
  tst_datum datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
		$this->recordset->execute($query);

		$query->setQueryOnce("CREATE TABLE IF NOT EXISTS testtable3 (
  tst_id int(11) NOT NULL,
  tst_id2 int(11) NOT NULL,
  tst_id3 int(11) DEFAULT NULL,
  tst_id4 int(11) DEFAULT NULL,
  PRIMARY KEY (tst_id,tst_id2)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
		$this->recordset->execute($query);

		$query->setQueryOnce("ALTER TABLE testtable3 ADD CONSTRAINT FK_314AF24EE913C2C8 FOREIGN KEY (tst_id4) REFERENCES testtable (tst_id);");
		$this->recordset->execute($query);

		$query->setQueryOnce("CREATE INDEX IDX_314AF24EE913C2C8 ON testtable3 (tst_id4);");
		$this->recordset->execute($query);

	}

	/**
	 * @return void
	 */
	protected function tearDown()
	{

		$query = $this->mysqlDatabase->newQuery();
		$query->setQueryOnce("DROP TABLE IF EXISTS testtable");
		$this->recordset->execute($query);
		$query->setQueryOnce("DROP TABLE IF EXISTS testtable2");
		$this->recordset->execute($query);
		$query->setQueryOnce("DROP TABLE IF EXISTS testtable3");
		$this->recordset->execute($query);

		Registry::reset();
		unset($this->mysqlDatabase);
		unset($this->query);
		unset($this->recordset);
	}

}
