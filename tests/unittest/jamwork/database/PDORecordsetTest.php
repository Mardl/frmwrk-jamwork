<?php

namespace unittest\jamwork\database;

use jamwork\database\PDODatabase;

/**
 * Class PDORecordsetTest
 *
 * @category Jamwork
 * @package  unittest\jamwork\database
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
class PDORecordsetTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var \jamwork\database\PDOQuery
	 */
	public $query = null;
	/**
	 * @var \jamwork\database\PDORecordset
	 */
	public $pdoRecordset = null;
	/**
	 * @var \jamwork\database\PDODatabase
	 */
	public $pdoDB = null;

	/**
	 * @return void
	 */
	public function testExecute()
	{
		$this->setExpectedException('\PDOException');
		$this->pdoRecordset->execute($this->query);
	}

	/**
	 * @return void
	 */
	public function testGet_OK()
	{
		$this->query->setQueryOnce("SHOW STATUS");
		$this->pdoRecordset->execute($this->query);
		$query = $this->pdoRecordset->get();
		$this->assertTrue(!empty($query));
		$this->assertInternalType('array', $query);
	}

	/**
	 * @return void
	 */
	public function testGet_FindNothing()
	{
		$this->query->select('*')->from('testtable')->addWhere('1','2');
		$this->pdoRecordset->execute($this->query);
		$query = $this->pdoRecordset->get();
		$this->assertFalse($query);
	}

	/**
	 * @return void
	 */
	public function testCount()
	{
		$table = 'testtable';
		$array = array('tst_id' => '1', 'tst_name' => 'Tester5', 'tst_datum' => '0000-00-00 00:00:00');
		$newId = $this->pdoDB->insert($table, $array);
		$this->query->from($table);
		$this->pdoRecordset->execute($this->query);
		$count = $this->pdoRecordset->count();
		$this->assertSame(1, $count);
		$array = array('tst_id' => '2', 'tst_name' => 'Tester5', 'tst_datum' => '0000-00-00 00:00:00');
		$newId = $this->pdoDB->insert($table, $array);
		$this->query->from($table);
		$count = $this->pdoRecordset->execute($this->query)->count();
		$this->assertSame(2, $count);
	}

	/**
	 * @return void
	 */
	public function testCount_Empty()
	{
		$this->query->select('*')->from('testtable')->addWhere('1','2');
		$this->pdoRecordset->execute($this->query);
		$count = $this->pdoRecordset->count();
		$this->assertSame(0, $count);
	}

	/**
	 * @return void
	 */
	public function testIsSuccessfull_positiv()
	{
		$table = 'testtable';
		$array = array('tst_name' => 'Tester5', 'tst_datum' => '0000-00-00 00:00:00');
		$newId = $this->pdoDB->insert($table, $array);
		$this->assertTrue($newId > 0);

		$this->query->select('*')->from('testtable');
		$ret = $this->pdoRecordset->execute($this->query)->isSuccessfull();
		$this->assertTrue($ret);

	}

	/**
	 * @return void
	 */
	public function testGet_data()
	{
		$table = 'testtable';
		$array = array('tst_name' => 'Tester5', 'tst_datum' => '0000-00-00 00:00:00');
		$newId = $this->pdoDB->insert($table, $array);
		$this->assertTrue($newId > 0);
		$array = array('tst_name' => 'foobar', 'tst_datum' => '0000-00-00 00:00:00');
		$newId = $this->pdoDB->insert($table, $array);
		$this->assertTrue($newId > 0);

		$shouldBe = array(
			array('tst_id' => '1', 'tst_name' => 'Tester5', 'tst_datum' => '0000-00-00 00:00:00'),
			array('tst_id' => '2', 'tst_name' => 'foobar', 'tst_datum' => '0000-00-00 00:00:00'),
		);

		$found = array();
		$this->query->select('*')->from('testtable');
		$this->pdoRecordset->execute($this->query);

		while (($row = $this->pdoRecordset->get()) == true)
		{
			$found[] = $row;
		}

		$this->assertSame($shouldBe, $found);

	}


	/**
	 * @return void
	 */
	public function testGetErrorMessage()
	{
		try
		{
			$this->query->select('*')->from('not_existing_table');
			$this->pdoRecordset->execute($this->query);
		} catch (\Exception $e)
		{

		}
		$errorMsg = $this->pdoRecordset->getErrorMessage();

		$this->assertSame("SQLSTATE[42S02]: Base table or view not found: 1146 Table 'test_jamwork.not_existing_table' doesn't exist", $errorMsg);
	}

	/**
	 * @return void
	 */
	public function testGetErrorNumber()
	{
		try
		{
			$this->query->select('*')->from('not_existing_table');
			$this->pdoRecordset->execute($this->query);
		} catch (\Exception $e)
		{

		}
		$errorNumber = $this->pdoRecordset->getErrorNumber();

		$this->assertSame('42S02',$errorNumber);
	}

	/**
	 * @return void
	 */
	protected function setUp()
	{
		$this->pdoDB = new PDODatabase('dakota.intern', 'test_jamwork', 'test_jamwork', 'test_jamwork');
		$this->query = $this->pdoDB->newQuery();
		$this->pdoRecordset = $this->pdoDB->newRecordSet();

		$query = $this->pdoDB->newQuery();
		$query->setQueryOnce("DROP TABLE IF EXISTS `testtable`");
		$this->pdoRecordset->execute($query);

		$query = $this->pdoDB->newQuery();
		$query->setQueryOnce("CREATE TABLE IF NOT EXISTS `testtable` (
  `tst_id` int(11) NOT NULL AUTO_INCREMENT,
  `tst_name` varchar(100) NOT NULL,
  `tst_datum` datetime NOT NULL,
  PRIMARY KEY (`tst_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
		$this->pdoRecordset->execute($query);

	}

	/**
	 * @return void
	 */
	protected function tearDown()
	{
		$query = $this->pdoDB->newQuery();
		$query->setQueryOnce("DROP TABLE IF EXISTS `testtable`");
		$this->pdoRecordset->execute($query);
		unset($this->pdoDB);
		unset($this->query);
		unset($this->pdoRecordset);
	}
}
