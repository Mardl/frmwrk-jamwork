<?php

namespace unittest\jamwork\database;

use jamwork\database\MysqlRecordset;
use jamwork\database\MysqlQuery;
use jamwork\database\MysqlDatabase;

class MysqlRecordsetTest extends \PHPUnit_Framework_TestCase 
{
	public $query = null;
	public $mysqlRecordset = null;
	public $mysqlDB = null;
		
	public function testExecute()
	{
		$this->mysqlRecordset->execute($this->query);
		$this->assertAttributeEquals($this->query,'query',$this->mysqlRecordset);		
		$this->assertAttributeEquals(false,'result',$this->mysqlRecordset);		
	}

	public function testGet_OK()
	{
		$this->query->setQueryOnce("SHOW STATUS");
		$this->mysqlRecordset->execute($this->query);
		$query = $this->mysqlRecordset->get();
		$this->assertTrue( !empty($query) );
		$this->assertInternalType('array', $query);
	}
	
	public function testGet_FindNothing()
	{
		$this->mysqlRecordset->execute($this->query);
		$query = $this->mysqlRecordset->get();
		$this->assertFalse( $query );
	}
	
	public function testCount()
	{
		$table = 'testtable';	
		$array = array('tst_id' => '1', 'tst_name' => 'Tester5', 'tst_datum' => '0000-00-00 00:00:00');
		$newId = $this->mysqlDB->insert($table, $array);
		$this->query->from($table);
		$count = $this->mysqlRecordset->execute($this->query)->count();
		$this->assertSame(1, $count );
		$array = array('tst_id' => '2', 'tst_name' => 'Tester5', 'tst_datum' => '0000-00-00 00:00:00');
		$newId = $this->mysqlDB->insert($table, $array);
		$this->query->from($table);
		$count = $this->mysqlRecordset->execute($this->query)->count();
		$this->assertSame(2, $count );
		$this->query->setQueryOnce('TRUNCATE TABLE `testtable`');
		$this->mysqlRecordset->execute($this->query);
	}
	
	public function testIsSuccessfull_negativ()
	{
		$this->query->select('*')->from('not_existing_table');
		$ret = $this->mysqlRecordset->execute($this->query)->isSuccessfull();
		$this->assertFalse($ret);
	}
	
	public function testIsSuccessfull_positiv()
	{
		$this->query->select('*')->from('testtable');
		$ret = $this->mysqlRecordset->execute($this->query)->isSuccessfull();
		$this->assertTrue($ret);
	}
	
	protected function setUp()
	{
		$this->mysqlDB = new MysqlDatabase('localhost', 'test_jamwork', 'test_jamwork', 'test_jamwork');
		$this->query = $this->mysqlDB->newQuery();
		$this->mysqlRecordset = $this->mysqlDB->newRecordSet();
	}
	
	protected function tearDown()
	{
		$this->query->setQueryOnce("TRUNCATE TABLE ".'testtable');
		$this->mysqlRecordset->execute($this->query);	
		unset($this->mysqlDB);
		unset($this->query);
		unset($this->mysqlRecordset);
	}
}
