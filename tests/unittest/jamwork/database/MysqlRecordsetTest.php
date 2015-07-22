<?php

namespace unittest\jamwork\database;

use jamwork\database\MysqlRecordset;
use jamwork\database\MysqlQuery;
use jamwork\database\MysqlDatabase;

/**
 * Class MysqlRecordsetTest
 *
 * @category Jamwork
 * @package  unittest\jamwork\database
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
class MysqlRecordsetTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var \jamwork\database\MysqlQuery
	 */
	public $query = null;
	/**
	 * @var \jamwork\database\MysqlRecordset
	 */
	public $mysqlRecordset = null;
	/**
	 * @var \jamwork\database\MysqlDatabase
	 */
	public $mysqlDB = null;

	/**
	 * @return void
	 */
	public function testExecute()
	{
		$this->mysqlRecordset->execute($this->query);
		$this->assertAttributeEquals($this->query, 'query', $this->mysqlRecordset);
		$this->assertAttributeEquals(false, 'result', $this->mysqlRecordset);
	}

	/**
	 * @return void
	 */
	public function testGet_OK()
	{
		$this->query->setQueryOnce("SHOW STATUS");
		$this->mysqlRecordset->execute($this->query);
		$query = $this->mysqlRecordset->get();
		$this->assertTrue(!empty($query));
		$this->assertInternalType('array', $query);
	}

	/**
	 * @return void
	 */
	public function testGet_FindNothing()
	{
		$this->mysqlRecordset->execute($this->query);
		$query = $this->mysqlRecordset->get();
		$this->assertFalse($query);
	}

	/**
	 * @return void
	 */
	public function testCount()
	{
		$table = 'testtable';
		$array = array('tst_id' => '1', 'tst_name' => 'Tester5', 'tst_datum' => '0000-00-00 00:00:00');
		$newId = $this->mysqlDB->insert($table, $array);
		$this->query->from($table);
		$this->mysqlRecordset->execute($this->query);
		$count = $this->mysqlRecordset->count();
		$this->assertSame(1, $count);
		$array = array('tst_id' => '2', 'tst_name' => 'Tester5', 'tst_datum' => '0000-00-00 00:00:00');
		$newId = $this->mysqlDB->insert($table, $array);
		$this->query->from($table);
		$count = $this->mysqlRecordset->execute($this->query)->count();
		$this->assertSame(2, $count);
	}

	/**
	 * @return void
	 */
	public function testCount_Empty()
	{
		$this->mysqlRecordset->execute($this->query);
		$count = $this->mysqlRecordset->count();
		$this->assertFalse($count);
	}

	/**
	 * @return void
	 */
	public function testIsSuccessfull_negativ()
	{
		$this->query->select('*')->from('not_existing_table');
		$ret = $this->mysqlRecordset->execute($this->query)->isSuccessfull();
		$this->assertFalse($ret);
	}

	/**
	 * @return void
	 */
	public function testIsSuccessfull_positiv()
	{
		$table = 'testtable';
		$array = array('tst_name' => 'Tester5', 'tst_datum' => '0000-00-00 00:00:00');
		$newId = $this->mysqlDB->insert($table, $array);
		$this->assertTrue($newId > 0);

		$this->query->select('*')->from('testtable');
		$ret = $this->mysqlRecordset->execute($this->query)->isSuccessfull();
		$this->assertTrue($ret);

	}

	/**
	 * @return void
	 */
	public function testGetErrorMessage()
	{
		$this->query->select('*')->from('not_existing_table');
		$this->mysqlRecordset->execute($this->query)->isSuccessfull();

		$this->assertSame("Table 'test_jamwork.not_existing_table' doesn't exist",$this->mysqlRecordset->getErrorMessage());
	}

	/**
	 * @return void
	 */
	public function testGetErrorNumber()
	{
		$this->query->select('*')->from('not_existing_table');
		$this->mysqlRecordset->execute($this->query)->isSuccessfull();

		$this->assertSame(1146,$this->mysqlRecordset->getErrorNumber());
	}

	/**
	 * @return void
	 */
	protected function setUp()
	{
		$this->mysqlDB = new MysqlDatabase('localhost', 'test_jamwork', 'test_jamwork', 'test_jamwork');
		$this->query = $this->mysqlDB->newQuery();
		$this->mysqlRecordset = $this->mysqlDB->newRecordSet();

		$query = $this->mysqlDB->newQuery();
		$query->setQueryOnce("DROP TABLE IF EXISTS `testtable`");
		$this->mysqlRecordset->execute($query);

		$query = $this->mysqlDB->newQuery();
		$query->setQueryOnce("CREATE TABLE IF NOT EXISTS `testtable` (
  `tst_id` int(11) NOT NULL AUTO_INCREMENT,
  `tst_name` varchar(100) NOT NULL,
  `tst_datum` datetime NOT NULL,
  PRIMARY KEY (`tst_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
		$this->mysqlRecordset->execute($query);

	}

	/**
	 * @return void
	 */
	protected function tearDown()
	{
		$query = $this->mysqlDB->newQuery();
		$query->setQueryOnce("DROP TABLE IF EXISTS `testtable`");
		$this->mysqlRecordset->execute($query);
		unset($this->mysqlDB);
		unset($this->query);
		unset($this->mysqlRecordset);
	}
}
