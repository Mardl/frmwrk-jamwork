<?php

namespace unittest\jamwork\database;

use jamwork\database\MysqlQuery;
use jamwork\database\MysqlDatabase;
use jamwork\common\Registry;

class MysqlQueryTest extends \PHPUnit_Framework_TestCase
{

	public function testFrom()
	{
		$sollFrom = 'db_info';
		$this->mysqlQuery->from($sollFrom);
		$this->assertAttributeEquals('db_info', 'table', $this->mysqlQuery);
	}

	public function testSelect_Array()
	{
		$sollSelect = array('header_one', 'header_one');
		$this->mysqlQuery->select($sollSelect);
		$this->assertAttributeEquals($sollSelect, 'fields', $this->mysqlQuery);
	}

	public function testSelect_String()
	{
		$sollSelect = 'header_one';
		$this->mysqlQuery->select($sollSelect);
		$this->assertAttributeEquals($sollSelect, 'fields', $this->mysqlQuery);
	}

	public function testWhere()
	{
		$sollWhere = 'id=42';
		$this->mysqlQuery->where($sollWhere);
		$this->assertAttributeEquals($sollWhere, 'clause', $this->mysqlQuery);
	}

	public function testSetQueryOnce()
	{
		$sollWhere = 'select bla und blub UNION';
		$this->mysqlQuery->setQueryOnce($sollWhere);
		$this->assertAttributeEquals($sollWhere, 'ownQuery', $this->mysqlQuery);
	}


	public function testGet_QueryOnce()
	{
		$sollWhereOnce = 'select bla und blub UNION';
		$sollFrom = 'db_info';
		$sollSelect = 'row1, row2';
		$sollWhere = 'id=42';

		$this->mysqlQuery->from($sollFrom)->select($sollSelect)->where($sollWhere);

		$this->mysqlQuery->setQueryOnce($sollWhereOnce);
		$this->assertsame($sollWhereOnce, $this->mysqlQuery->get());

		// beim 2ten Lauf wieder standard
		//$this->assertsame ('SELECT '.$sollSelect.' FROM '.$sollFrom.' WHERE '.$sollWhere, $this->mysqlQuery->get());

		//der Query Once ist in dem Fall höher priorisiert!
		$this->assertsame($sollWhereOnce, $this->mysqlQuery->get());
	}

	public function testSetQueryOnceExpException()
	{
		$sollWhereOnce = 'select bla und blub';

		try
		{
			$this->mysqlQuery->setQueryOnce($sollWhereOnce);
		} catch (\Exception $e)
		{
			return;
		}

		// hier darf er nie hinkommen!
		$this->assertTrue(false);
	}

	public function testGet_Default()
	{
		$sollFrom = 'db_info';
		$sollSelect = 'row1, row2';
		$sollWhere = 'id=42';

		$query = $this->mysqlQuery->from($sollFrom)->select($sollSelect)->where($sollWhere);
		$this->assertsame('SELECT ' . $sollSelect . ' FROM ' . $sollFrom . ' WHERE ' . $sollWhere, $query->get());
	}

	public function testGet_Default_withoutWhere()
	{
		$sollFrom = 'db_info';
		$sollSelect = 'row1, row2';

		$query = $this->mysqlQuery->from($sollFrom)->select($sollSelect);
		$this->assertsame('SELECT ' . $sollSelect . ' FROM ' . $sollFrom, $query->get());
	}

	public function testOrderBy()
	{
		$sollFrom = 'db_info';
		$sollSelect = 'row1, row2';
		$sollOrder = 'order asc';

		$query = $this->mysqlQuery->from($sollFrom)->select($sollSelect)->orderBy('order asc');
		$this->assertsame('SELECT ' . $sollSelect . ' FROM ' . $sollFrom . ' ORDER BY ' . $sollOrder, $query->get());
	}

	public function testJoin()
	{
		$sollFrom = 'db_info';
		$sollSelect = 'row1, row2';
		$sollOrder = 'order asc';
		$solljointable = 'db_table';
		$solljoinon = 'foo = boof';

		$query = $this->mysqlQuery->from($sollFrom)->select($sollSelect)->join($solljointable)->on($solljoinon);
		$this->assertsame('SELECT ' . $sollSelect . ' FROM ' . $sollFrom . ' LEFT JOIN ' . $solljointable . ' ON ' . $solljoinon . ' ', $query->get());

		$this->mysqlQuery = new MysqlQuery($this->db);
		$query = $this->mysqlQuery->from($sollFrom)->select($sollSelect)->join($solljointable)->on($solljoinon)->join($solljointable)->on($solljoinon);
		$this->assertsame('SELECT ' . $sollSelect . ' FROM ' . $sollFrom . ' LEFT JOIN ' . $solljointable . ' ON ' . $solljoinon . ' ' . ' LEFT JOIN ' . $solljointable . ' ON ' . $solljoinon . ' ', $query->get());

		$this->mysqlQuery = new MysqlQuery($this->db);
		$query = $this->mysqlQuery->from($sollFrom)->select($sollSelect)->join($solljointable)->on($solljoinon)->where(" a = b");

		$this->assertsame('SELECT ' . $sollSelect . ' FROM ' . $sollFrom . ' LEFT JOIN ' . $solljointable . ' ON ' . $solljoinon . '  WHERE  a = b', $query->get());
	}

	public function testOn()
	{
		// wird im testJoin mit getestet;
	}

	public function testLimit()
	{
		$obj = $this->mysqlQuery->limit(5);
		$limit = $this->readAttribute($this->mysqlQuery, 'limit');
		$this->assertSame(5, $limit[0]);
		$this->assertSame(1, count($limit));

		$obj = $this->mysqlQuery->limit(5, 10);
		$limit = $this->readAttribute($this->mysqlQuery, 'limit');
		$this->assertSame(5, $limit[0]);
		$this->assertSame(10, $limit[1]);

		$this->assertSame($obj, $this->mysqlQuery);
	}

	protected function setUp()
	{
		$this->db = new MysqlDatabase('localhost', 'test_jamwork', 'test_jamwork', 'test_jamwork');

		$registry = Registry::getInstance();
		$registry->setDatabase($this->db);

		$this->mysqlQuery = new MysqlQuery($this->db);
	}

	protected function tearDown()
	{
		unset($this->mysqlQuery);
		unset($this->db);
	}
}