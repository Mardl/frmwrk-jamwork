<?php

namespace unittest\jamwork\database;

use jamwork\database\MysqlQuery;
use jamwork\database\MysqlDatabase;
use jamwork\common\Registry;

/**
 * Class MysqlQueryTest
 *
 * @category Jamwork
 * @package  unittest\jamwork\database
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
class MysqlQueryTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @return void
	 */
	public function testFrom()
	{
		$sollFrom = 'db_info';
		$this->mysqlQuery->from($sollFrom);
		$this->assertAttributeEquals('db_info', 'table', $this->mysqlQuery);
	}

	/**
	 * @return void
	 */
	public function testSelect_Array()
	{
		$sollSelect = array('header_one', 'header_one');
		$this->mysqlQuery->select($sollSelect);
		$this->assertAttributeEquals($sollSelect, 'fields', $this->mysqlQuery);
		$this->assertSame('SELECT header_one,header_one FROM ',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testSelect_String()
	{
		$sollSelect = 'header_one';
		$this->mysqlQuery->select($sollSelect);
		$this->assertAttributeEquals($sollSelect, 'fields', $this->mysqlQuery);
	}

	/**
	 * @return void
	 */
	public function testUpdate()
	{
		$table = 'db_info';
		$this->mysqlQuery->update($table);
		$this->assertAttributeEquals($table, 'table', $this->mysqlQuery);
	}

	/**
	 * @return void
	 */
	public function testWhere()
	{
		$sollWhere = 'id=42';
		$this->mysqlQuery->where($sollWhere);
		$this->assertAttributeEquals($sollWhere, 'clause', $this->mysqlQuery);
	}

	/**
	 * @return void
	 */
	public function testOpenClosure()
	{
		$reflectedProperty = new \ReflectionProperty($this->mysqlQuery, 'openClosure');
		$reflectedProperty->setAccessible(true);
		$openClosure = $reflectedProperty->getValue($this->mysqlQuery);

		$this->assertFalse($openClosure);
		$this->mysqlQuery->openClosure();

		$openClosure = $reflectedProperty->getValue($this->mysqlQuery);

		$this->assertTrue($openClosure);
	}

	/**
	 * @return void
	 */
	public function testCloseClosure()
	{

		$this->mysqlQuery->closeClosure();


		$this->assertSame('SELECT * FROM  WHERE  )',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testSet()
	{
		$field = 'header_one';
		$value = 'test';
		$this->mysqlQuery->set($field, $value);
		$this->assertAttributeEquals(array($field => $value), 'sets', $this->mysqlQuery);
	}

	/**
	 * @return void
	 */
	public function testAddWhere_twice()
	{
		$this->mysqlQuery->addWhere('feld1',"erg1");
		$this->mysqlQuery->addWhere('feld2',"erg2");
		$this->assertSame('SELECT * FROM  WHERE feld1 = "erg1" AND feld2 = "erg2"',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhere_numeric()
	{
		$this->mysqlQuery->addWhere('feld2',88);
		$this->assertSame('SELECT * FROM  WHERE feld2 = 88',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhere_numericAsString()
	{
		$this->mysqlQuery->addWhere('feld2',"88");
		$this->assertSame('SELECT * FROM  WHERE feld2 = "88"',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhere_array()
	{
		$this->mysqlQuery->addWhere('feld2',array(1,2,3));
		$this->assertSame('SELECT * FROM  WHERE feld2 IN (1,2,3)',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhere_valueobject()
	{
		try
		{
			$this->mysqlQuery->addWhere('feld2',$this);
		} catch (\InvalidArgumentException $e)
		{
			return;
		}

		$this->fail('An expected Exception has not been raised.');
	}

	/**
	 * @return void
	 */
	public function testInnerStatement()
	{
		$this->mysqlQuery->innerStatement('feld2','select 1');
		$this->assertSame('SELECT * FROM  WHERE feld2 IN (select 1) ',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testIn()
	{
		$ret = $this->mysqlQuery->in('feld2',array('as','df','qwert'));
		$this->assertSame("feld2 IN ('as','df','qwert')",$ret);

		// die clause wird NICHT erweitert!
		$this->assertSame('SELECT * FROM ',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhere()
	{
		try
		{
			$this->mysqlQuery->addWhere('feld1',null);
		} catch (\Exception $e)
		{
			return;
		}

		$this->fail('An expected Exception has not been raised.');
	}

	/**
	 * @return void
	 */
	public function testAddWhere_OpenClosure()
	{
		$this->mysqlQuery->openClosure();
		$this->mysqlQuery->addWhere('feld2',"erg2");
		$this->assertSame('SELECT * FROM  WHERE  (feld2 = "erg2"',$this->mysqlQuery->get());
	}


	/**
	 * @return void
	 */
	public function testAddWhereIsNull()
	{
		$this->mysqlQuery->addWhereIsNull('feld1');
		$this->assertSame('SELECT * FROM  WHERE feld1 IS NULL ',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhereIsNull_Equal()
	{
		$this->mysqlQuery->addWhereIsNull('feld1','=');
		$this->assertSame('SELECT * FROM  WHERE feld1 = NULL ',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhereIsNull_Twice()
	{
		$this->mysqlQuery->addWhereIsNull('feld1','=');
		$this->assertSame('SELECT * FROM  WHERE feld1 = NULL ',$this->mysqlQuery->get());

		$this->mysqlQuery->addWhereIsNull('feld2','is');
		$this->assertSame('SELECT * FROM  WHERE feld1 = NULL  AND feld2 is NULL ',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhereIsNull_TwiceConcat()
	{
		$this->mysqlQuery->addWhereIsNull('feld1','=');
		$this->assertSame('SELECT * FROM  WHERE feld1 = NULL ',$this->mysqlQuery->get());

		$this->mysqlQuery->addWhereIsNull('feld2','is','OR');
		$this->assertSame('SELECT * FROM  WHERE feld1 = NULL  OR feld2 is NULL ',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhereIsNull_OpenClosure()
	{
		$this->mysqlQuery->openClosure();
		$this->mysqlQuery->addWhereIsNull('feld1','=');
		$this->assertSame('SELECT * FROM  WHERE  (feld1 = NULL ',$this->mysqlQuery->get());
	}


	/**
	 * @return void
	 */
	public function testAddWhereFunc()
	{
		$this->mysqlQuery->addWhereFunc('feld1','NOW()');
		$this->assertSame('SELECT * FROM  WHERE feld1 = NOW() ',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhereFunc_Equal()
	{
		$this->mysqlQuery->addWhereFunc('feld1','NOW()');
		$this->assertSame('SELECT * FROM  WHERE feld1 = NOW() ',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhereFunc_Twice()
	{
		$this->mysqlQuery->addWhereFunc('feld1','NOW()');
		$this->assertSame('SELECT * FROM  WHERE feld1 = NOW() ',$this->mysqlQuery->get());

		$this->mysqlQuery->addWhereFunc('feld2','DATE()','is');
		$this->assertSame('SELECT * FROM  WHERE feld1 = NOW()  AND feld2 is DATE() ',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhereFunc_TwiceConcat()
	{
		$this->mysqlQuery->addWhereFunc('feld1','NOW()');
		$this->assertSame('SELECT * FROM  WHERE feld1 = NOW() ',$this->mysqlQuery->get());

		$this->mysqlQuery->addWhereFunc('feld2','DATE()','is','or');
		$this->assertSame('SELECT * FROM  WHERE feld1 = NOW()  or feld2 is DATE() ',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhereFunc_OpenClosure()
	{
		$this->mysqlQuery->openClosure();
		$this->mysqlQuery->addWhereFunc('feld1','NOW()','<');
		$this->assertSame('SELECT * FROM  WHERE  (feld1 < NOW() ',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhereFunc_OpenClosureTwice()
	{
		$this->mysqlQuery->addWhereFunc('feld1','NOW()','<');
		$this->assertSame('SELECT * FROM  WHERE feld1 < NOW() ',$this->mysqlQuery->get());

		$this->mysqlQuery->openClosure();
		$this->mysqlQuery->addWhereFunc('feld2','DATE()','is','or');
		$this->assertSame('SELECT * FROM  WHERE feld1 < NOW()  or (feld2 is DATE() ',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhereLike()
	{
		$this->mysqlQuery->addWhereLike('feld1','test');
		$this->assertSame('SELECT * FROM  WHERE feld1 LIKE "%test%" ',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhereLike_Twice()
	{
		$this->mysqlQuery->addWhereLike('feld1','test');
		$this->assertSame('SELECT * FROM  WHERE feld1 LIKE "%test%" ',$this->mysqlQuery->get());

		$this->mysqlQuery->addWhereLike('feld2','test2','%s');
		$this->assertSame('SELECT * FROM  WHERE feld1 LIKE "%test%"  AND feld2 LIKE "test2" ',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhereLike_TwiceConcat()
	{
		$this->mysqlQuery->addWhereLike('feld1','test');
		$this->assertSame('SELECT * FROM  WHERE feld1 LIKE "%test%" ',$this->mysqlQuery->get());

		$this->mysqlQuery->addWhereLike('feld2','test2','%s','or');
		$this->assertSame('SELECT * FROM  WHERE feld1 LIKE "%test%"  or feld2 LIKE "test2" ',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhereLike_OpenClosure()
	{
		$this->mysqlQuery->openClosure();
		$this->mysqlQuery->addWhereLike('feld1','unittest');
		$this->assertSame('SELECT * FROM  WHERE  (feld1 LIKE "%unittest%" ',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhereBetween_Exception()
	{
		try
		{
			$this->mysqlQuery->addWhereBetween('feld1','as',2);
		} catch (\InvalidArgumentException $e)
		{
			return;
		}

		$this->fail('An expected Exception has not been raised.');
	}

	/**
	 * @return void
	 */
	public function testAddWhereBetween_numeric()
	{
		$this->mysqlQuery->addWhereBetween('feld1',1,2);

		$this->assertSame('SELECT * FROM  WHERE feld1 between 1 AND 2',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhereBetween_numericAsString()
	{
		$this->mysqlQuery->addWhereBetween('feld1','1',2);

		$this->assertSame('SELECT * FROM  WHERE feld1 between "1" AND "2"',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhereBetween_string()
	{
		$this->mysqlQuery->addWhereBetween('feld1','A','z');

		$this->assertSame('SELECT * FROM  WHERE feld1 between "A" AND "z"',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhereBetween_stringexcaped()
	{
		$this->mysqlQuery->addWhereBetween('feld1','A"sd','z20as');

		$this->assertSame('SELECT * FROM  WHERE feld1 between "A\"sd" AND "z20as"',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhereBetween_twice()
	{
		$this->mysqlQuery->addWhere('test','unit');

		$this->mysqlQuery->addWhereBetween('feld1','A"sd','z20as');

		$this->assertSame('SELECT * FROM  WHERE test = "unit" AND feld1 between "A\"sd" AND "z20as"',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testAddWhereBetween_openClosure()
	{
		$this->mysqlQuery->openClosure();

		$this->mysqlQuery->addWhereBetween('feld1','A"sd','z20as');

		$this->assertSame('SELECT * FROM  WHERE  (feld1 between "A\"sd" AND "z20as"',$this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testSetQueryOnce()
	{
		$sollWhere = 'select bla und blub UNION';
		$this->mysqlQuery->setQueryOnce($sollWhere);
		$this->assertAttributeEquals($sollWhere, 'ownQuery', $this->mysqlQuery);
	}

	/**
	 * @return void
	 */
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

		$this->fail('An expected Exception has not been raised.');
	}


	/**
	 * @return void
	 */
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

	/**
	 * @return void
	 */
	public function testGet_update()
	{
		$this->mysqlQuery->update('unittest');
		$this->assertsame('UPDATE unittest SET ', $this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testGet_UpdateQueryOnce()
	{
		$this->mysqlQuery->update('unittest');
		$this->mysqlQuery->setQueryOnce('blablablaFromUpdate');
		$this->assertsame('blablablaFromUpdate', $this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testGet_UpdateWhere()
	{
		$this->mysqlQuery->update('unittest');
		$this->mysqlQuery->addWhere('unit',1);
		$this->assertsame('UPDATE unittest SET  WHERE unit = 1', $this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testGet_UpdateSet()
	{
		$this->mysqlQuery->update('unittest');
		$this->mysqlQuery->set('unit',99);
		$this->assertsame('UPDATE unittest SET unit = 99', $this->mysqlQuery->get());

		$this->mysqlQuery->addWhere('unit',1);
		$this->assertsame('UPDATE unittest SET unit = 99 WHERE unit = 1', $this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testGet_UpdateSetTwice()
	{
		$this->mysqlQuery->update('unittest');
		$this->mysqlQuery->set('unit',99);
		$this->mysqlQuery->set('antwort',42);
		$this->assertsame('UPDATE unittest SET unit = 99, antwort = 42', $this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testGet_UpdateSetNULL()
	{
		$this->mysqlQuery->update('unittest');
		$this->mysqlQuery->set('unit',null);
		$this->assertsame('UPDATE unittest SET unit = NULL', $this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testGet_UpdateSetString()
	{
		$this->mysqlQuery->update('unittest');
		$this->mysqlQuery->set('unit','test');
		$this->assertsame('UPDATE unittest SET unit = "test"', $this->mysqlQuery->get());

		$this->mysqlQuery->set('unit','A"sd');
		$this->assertsame('UPDATE unittest SET unit = "A\"sd"', $this->mysqlQuery->get());

		$this->mysqlQuery->set('unit',"A'sd");
		$this->assertsame('UPDATE unittest SET unit = "A\\\'sd"', $this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	public function testGet_UpdateSetObject()
	{
		$this->mysqlQuery->update('unittest');
		$this->mysqlQuery->set('unit',array());
		try
		{
			$this->assertsame('UPDATE unittest SET unit = "test"', $this->mysqlQuery->get());
		} catch (\InvalidArgumentException $e)
		{
			return;
		}

		$this->fail('An expected Exception has not been raised.');

	}

	/**
	 * @return void
	 */
	public function testGet_Default()
	{
		$sollFrom = 'db_info';
		$sollSelect = 'row1, row2';
		$sollWhere = 'id=42';

		$query = $this->mysqlQuery->from($sollFrom)->select($sollSelect)->where($sollWhere);
		$this->assertsame('SELECT ' . $sollSelect . ' FROM ' . $sollFrom . ' WHERE ' . $sollWhere, $query->get());
	}

	/**
	 * @return void
	 */
	public function testGet_Default_withoutWhere()
	{
		$sollFrom = 'db_info';
		$sollSelect = 'row1, row2';

		$query = $this->mysqlQuery->from($sollFrom)->select($sollSelect);
		$this->assertsame('SELECT ' . $sollSelect . ' FROM ' . $sollFrom, $query->get());
	}

	/**
	 * @return void
	 */
	public function testGet_distinct()
	{
		$sollFrom = 'db_info';
		$sollSelect = 'row1, row2';

		$query = $this->mysqlQuery->distinct()->from($sollFrom)->select($sollSelect);
		$this->assertsame('SELECT DISTINCT ' . $sollSelect . ' FROM ' . $sollFrom, $query->get());
	}

	/**
	 * @return void
	 */
	public function testaddHaving()
	{
		$query = $this->mysqlQuery->addHaving('unit','test');
		$this->assertsame('SELECT * FROM  HAVING unit = "test"', $query->get());
	}

	/**
	 * @return void
	 */
	public function testaddHaving_numeric()
	{
		$query = $this->mysqlQuery->addHaving('unit',5);
		$this->assertsame('SELECT * FROM  HAVING unit = 5', $query->get());
	}

	/**
	 * @return void
	 */
	public function testaddHaving_isnull()
	{
		$query = $this->mysqlQuery->addHaving('unit',null);
		$this->assertsame('SELECT * FROM  HAVING unit = NULL', $query->get());

		$query = $this->mysqlQuery->addHaving('unit2',null,'IS');
		$this->assertsame('SELECT * FROM  HAVING unit = NULL AND unit2 IS NULL', $query->get());
	}

	/**
	 * @return void
	 */
	public function testaddHaving_concat()
	{
		$query = $this->mysqlQuery->addHaving('unit',null);
		$this->assertsame('SELECT * FROM  HAVING unit = NULL', $query->get());

		$query = $this->mysqlQuery->addHaving('unit2',8,'=','OR');
		$this->assertsame('SELECT * FROM  HAVING unit = NULL OR unit2 = 8', $query->get());
	}

	/**
	 * @return void
	 */
	public function testaddHaving_Exception()
	{
		try
		{
			$this->mysqlQuery->addHaving('unit',array());
		} catch (\InvalidArgumentException $e)
		{
			return;
		}

		$this->fail('An expected Exception has not been raised.');
	}

	/**
	 * @return void
	 */
	public function testOrderBy()
	{
		$sollFrom = 'db_info';
		$sollSelect = 'row1, row2';
		$sollOrder = 'order asc';

		$query = $this->mysqlQuery->from($sollFrom)->select($sollSelect)->orderBy('order asc');
		$this->assertsame('SELECT ' . $sollSelect . ' FROM ' . $sollFrom . ' ORDER BY ' . $sollOrder, $query->get());
	}

	/**
	 * @return void
	 */
	public function testGroupBy()
	{
		$sollFrom = 'db_info';
		$sollSelect = 'row1, row2';
		$sollOrder = 'order asc';

		$query = $this->mysqlQuery->from($sollFrom)->select($sollSelect)->groupBy('order asc');
		$this->assertsame('SELECT ' . $sollSelect . ' FROM ' . $sollFrom . ' GROUP BY ' . $sollOrder, $query->get());
	}

	/**
	 * @return void
	 */
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

	/**
	 * @return void
	 */
	public function testJoin_Right()
	{
		$sollFrom = 'db_info';
		$sollSelect = 'row1, row2';
		$solljointable = 'db_table';
		$solljoinon = 'foo = boof';

		$query = $this->mysqlQuery->from($sollFrom)->select($sollSelect)->join($solljointable,'RIGHT')->on($solljoinon);
		$this->assertsame('SELECT ' . $sollSelect . ' FROM ' . $sollFrom . ' RIGHT JOIN ' . $solljointable . ' ON ' . $solljoinon . ' ', $query->get());
	}

	/**
	 * @return void
	 */
	public function testLeftJoin()
	{
		$sollFrom = 'db_info';
		$sollSelect = 'row1, row2';
		$solljointable = 'db_table';
		$solljoinon = 'foo = boof';

		$query = $this->mysqlQuery->from($sollFrom)->select($sollSelect)->leftjoin($solljointable)->on($solljoinon);
		$this->assertsame('SELECT ' . $sollSelect . ' FROM ' . $sollFrom . ' LEFT JOIN ' . $solljointable . ' ON ' . $solljoinon . ' ', $query->get());
	}

	/**
	 * @return void
	 */
	public function testInnerJoin()
	{
		$sollFrom = 'db_info';
		$sollSelect = 'row1, row2';
		$solljointable = 'db_table';
		$solljoinon = 'foo = boof';

		$query = $this->mysqlQuery->from($sollFrom)->select($sollSelect)->innerjoin($solljointable)->on($solljoinon);
		$this->assertsame('SELECT ' . $sollSelect . ' FROM ' . $sollFrom . ' INNER JOIN ' . $solljointable . ' ON ' . $solljoinon . ' ', $query->get());
	}

	/**
	 * @return void
	 */
	public function testLimit()
	{
		$this->mysqlQuery->limit(5);
		$limit = $this->readAttribute($this->mysqlQuery, 'limit');
		$this->assertSame(5, $limit[0]);
		$this->assertSame(1, count($limit));

//		$this->assertSame('SELECT * FROM  LIMIT 5', $this->mysqlQuery->get());

		$this->mysqlQuery->limit(5, 10);
		$limit = $this->readAttribute($this->mysqlQuery, 'limit');
		$this->assertSame(5, $limit[0]);
		$this->assertSame(10, $limit[1]);

		$this->assertSame('SELECT * FROM  LIMIT 5, 10', $this->mysqlQuery->get());
	}

	/**
	 * @return void
	 */
	protected function setUp()
	{
		$this->db = new MysqlDatabase('yuma.intern', 'root', '3werken', 'test_jamwork');

		$registry = Registry::getInstance();
		$registry->setDatabase($this->db);

		$this->mysqlQuery = new MysqlQuery($this->db);
	}

	/**
	 * @return void
	 */
	protected function tearDown()
	{
		unset($this->mysqlQuery);
		unset($this->db);
	}
}