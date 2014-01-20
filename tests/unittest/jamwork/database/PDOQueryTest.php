<?php

namespace unittest\jamwork\database;

use jamwork\database\PDOQuery;
use jamwork\database\PDODatabase;

/**
 * Class PDOQueryTest
 *
 * @category Jamwork
 * @package  unittest\jamwork\database
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
class PDOQueryTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var PDOQuery
	 */
	private $pdoQuery;

	/**
	 * @return void
	 */
	public function testFrom()
	{
		$sollFrom = 'db_info';
		$this->pdoQuery->from($sollFrom);
		$this->assertAttributeEquals('db_info', 'table', $this->pdoQuery);
	}

	/**
	 * @return void
	 */
	public function testSelect_Array()
	{
		$sollSelect = array('header_one', 'header_one');
		$this->pdoQuery->select($sollSelect);
		$this->assertAttributeEquals($sollSelect, 'fields', $this->pdoQuery);
		$this->assertSame('SELECT header_one,header_one', $this->pdoQuery->get());
	}

	/**
	 * @return void
	 */
	public function testSelect_String()
	{
		$sollSelect = 'header_one';
		$this->pdoQuery->select($sollSelect);
		$this->assertAttributeEquals($sollSelect, 'fields', $this->pdoQuery);
	}

	/**
	 * @return void
	 */
	public function testWhere()
	{
		$sollWhere = 'id=42';
		$this->pdoQuery->where($sollWhere);
		$this->assertAttributeEquals($sollWhere, 'clause', $this->pdoQuery);
	}

	/**
	 * @return void
	 */
	public function testOpenClosure()
	{
		$reflectedProperty = new \ReflectionProperty($this->pdoQuery, 'openClosure');
		$reflectedProperty->setAccessible(true);
		$openClosure = $reflectedProperty->getValue($this->pdoQuery);

		$this->assertFalse($openClosure);
		$this->pdoQuery->openClosure();

		$openClosure = $reflectedProperty->getValue($this->pdoQuery);

		$this->assertTrue($openClosure);
	}

	/**
	 * @return void
	 */
	public function testCloseClosure()
	{

		$this->pdoQuery->closeClosure();


		$this->assertSame('SELECT * WHERE  )', $this->pdoQuery->get());
	}

	/**
	 * @param string $stmt
	 * @param array  $keyValuePair
	 * @return void
	 */
	private function replaceBind($keyValuePair, $stmt)
	{
		foreach ($keyValuePair as $key => $value)
		{
			$stmt = str_replace($key, $value, $stmt);
		}

		return $stmt;
	}

	/**
	 * @return void
	 */
	public function testAddWhere_twice()
	{
		$this->pdoQuery->addWhere('feld1', "erg1");
		$this->pdoQuery->addWhere('feld2', "erg2");
		$this->assertSame('SELECT * WHERE feld1 = erg1 AND feld2 = erg2', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhere_numeric()
	{
		$this->pdoQuery->addWhere('feld2', 88);
		$this->assertSame('SELECT * WHERE feld2 = 88', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhere_numericAsString()
	{
		$this->pdoQuery->addWhere('feld2', "88");
		$this->assertSame('SELECT * WHERE feld2 = 88', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhere_array()
	{
		$this->pdoQuery->addWhere('feld2', array(1, 2, 3));
		$this->assertSame('SELECT * WHERE feld2 IN (1,2,3)', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhere_valueobject()
	{
		try
		{
			$this->pdoQuery->addWhere('feld2', $this);
		} catch (\InvalidArgumentException $e)
		{
			return;
		}

		$this->fail('An expected Exception has not been raised.');
	}

	/**
	 * @return void
	 */
	public function testInnerStatement_Exception()
	{
		$this->setExpectedException('\Exception');
		$this->pdoQuery->innerStatement('feld2', 'select 1');
	}

	/**
	 * @return void
	 */
	public function testInnerStatement()
	{
		$pdoQuery = new PDOQuery();
		$pdoQuery->select(1);
		$this->pdoQuery->innerStatement('feld2', $pdoQuery);
		$this->assertSame('SELECT * WHERE feld2 IN (SELECT 1)', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testIn()
	{
		$ret = $this->pdoQuery->in('feld2', array('as', 'df', 'qwert'));
		$this->assertSame("feld2 IN (as,df,qwert)", $this->replaceBind($this->pdoQuery->getBindValues(), $ret));

		// die clause wird NICHT erweitert!
		$this->assertSame('SELECT *', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhere()
	{
		try
		{
			$this->pdoQuery->addWhere('feld1', null);
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
		$this->pdoQuery->openClosure();
		$this->pdoQuery->addWhere('feld2', "erg2");
		$this->assertSame('SELECT * WHERE  (feld2 = erg2', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}


	/**
	 * @return void
	 */
	public function testAddWhereIsNull()
	{
		$this->pdoQuery->addWhereIsNull('feld1');
		$this->assertSame('SELECT * WHERE feld1 IS NULL', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhereIsNull_Equal()
	{
		$this->pdoQuery->addWhereIsNull('feld1', '=');
		$this->assertSame('SELECT * WHERE feld1 = NULL', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhereIsNull_Twice()
	{
		$this->pdoQuery->addWhereIsNull('feld1', '=');
		$this->assertSame('SELECT * WHERE feld1 = NULL', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));

		$this->pdoQuery->addWhereIsNull('feld2', 'is');
		$this->assertSame('SELECT * WHERE feld1 = NULL  AND feld2 is NULL', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhereIsNull_TwiceConcat()
	{
		$this->pdoQuery->addWhereIsNull('feld1', '=');
		$this->assertSame('SELECT * WHERE feld1 = NULL', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));

		$this->pdoQuery->addWhereIsNull('feld2', 'is', 'OR');
		$this->assertSame('SELECT * WHERE feld1 = NULL  OR feld2 is NULL', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhereIsNull_OpenClosure()
	{
		$this->pdoQuery->openClosure();
		$this->pdoQuery->addWhereIsNull('feld1', '=');
		$this->assertSame('SELECT * WHERE  (feld1 = NULL', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}


	/**
	 * @return void
	 */
	public function testAddWhereFunc()
	{
		$this->pdoQuery->addWhereFunc('feld1', 'NOW()');
		$this->assertSame('SELECT * WHERE feld1 = NOW()', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhereFunc_Equal()
	{
		$this->pdoQuery->addWhereFunc('feld1', 'NOW()');
		$this->assertSame('SELECT * WHERE feld1 = NOW()', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhereFunc_Twice()
	{
		$this->pdoQuery->addWhereFunc('feld1', 'NOW()');
		$this->assertSame('SELECT * WHERE feld1 = NOW()', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));

		$this->pdoQuery->addWhereFunc('feld2', 'DATE()', 'is');
		$this->assertSame('SELECT * WHERE feld1 = NOW()  AND feld2 is DATE()', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhereFunc_TwiceConcat()
	{
		$this->pdoQuery->addWhereFunc('feld1', 'NOW()');
		$this->assertSame('SELECT * WHERE feld1 = NOW()', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));

		$this->pdoQuery->addWhereFunc('feld2', 'DATE()', 'is', 'or');
		$this->assertSame('SELECT * WHERE feld1 = NOW()  or feld2 is DATE()', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhereFunc_OpenClosure()
	{
		$this->pdoQuery->openClosure();
		$this->pdoQuery->addWhereFunc('feld1', 'NOW()', '<');
		$this->assertSame('SELECT * WHERE  (feld1 < NOW()', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhereFunc_OpenClosureTwice()
	{
		$this->pdoQuery->addWhereFunc('feld1', 'NOW()', '<');
		$this->assertSame('SELECT * WHERE feld1 < NOW()', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));

		$this->pdoQuery->openClosure();
		$this->pdoQuery->addWhereFunc('feld2', 'DATE()', 'is', 'or');
		$this->assertSame('SELECT * WHERE feld1 < NOW()  or (feld2 is DATE()', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhereLike()
	{
		$this->pdoQuery->addWhereLike('feld1', 'test');
		$this->assertSame('SELECT * WHERE feld1 LIKE %test%', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhereLike_Twice()
	{
		$this->pdoQuery->addWhereLike('feld1', 'test');
		$this->assertSame('SELECT * WHERE feld1 LIKE %test%', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));

		$this->pdoQuery->addWhereLike('feld2', 'test2', '%s');
		$this->assertSame('SELECT * WHERE feld1 LIKE %test%  AND feld2 LIKE test2', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhereLike_TwiceConcat()
	{
		$this->pdoQuery->addWhereLike('feld1', 'test');
		$this->assertSame('SELECT * WHERE feld1 LIKE %test%', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));

		$this->pdoQuery->addWhereLike('feld2', 'test2', '%s', 'or');
		$this->assertSame('SELECT * WHERE feld1 LIKE %test%  or feld2 LIKE test2', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhereLike_OpenClosure()
	{
		$this->pdoQuery->openClosure();
		$this->pdoQuery->addWhereLike('feld1', 'unittest');
		$this->assertSame('SELECT * WHERE  (feld1 LIKE %unittest%', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhereBetween_Exception()
	{
		try
		{
			$this->pdoQuery->addWhereBetween('feld1', 'as', 2);
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
		$this->pdoQuery->addWhereBetween('feld1', 1, 2);

		$this->assertSame('SELECT * WHERE feld1 between 1 AND 2', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhereBetween_numericAsString()
	{
		$this->pdoQuery->addWhereBetween('feld1', '1', 2);

		$this->assertSame('SELECT * WHERE feld1 between 1 AND 2', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhereBetween_string()
	{
		$this->pdoQuery->addWhereBetween('feld1', 'A', 'z');

		$this->assertSame('SELECT * WHERE feld1 between A AND z', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhereBetween_stringexcaped()
	{
		$this->pdoQuery->addWhereBetween('feld1', 'A"sd', 'z20as');

		$this->assertSame('SELECT * WHERE feld1 between A"sd AND z20as', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhereBetween_twice()
	{
		$this->pdoQuery->addWhere('test', 'unit');

		$this->pdoQuery->addWhereBetween('feld1', 'A"sd', 'z20as');

		$this->assertSame('SELECT * WHERE test = unit AND feld1 between A"sd AND z20as', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testAddWhereBetween_openClosure()
	{
		$this->pdoQuery->openClosure();

		$this->pdoQuery->addWhereBetween('feld1', 'A"sd', 'z20as');

		$this->assertSame('SELECT * WHERE  (feld1 between A"sd AND z20as', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testSetQueryOnce()
	{
		$sollWhere = 'select bla und blub UNION';
		$this->pdoQuery->setQueryOnce($sollWhere);
		$this->assertAttributeEquals($sollWhere, 'ownQuery', $this->pdoQuery);
	}

	/**
	 * @return void
	 */
	public function testSetQueryOnceExpException()
	{
		$sollWhereOnce = 'select bla und blub';

		try
		{
			$this->pdoQuery->setQueryOnce($sollWhereOnce);
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

		$this->pdoQuery->from($sollFrom)->select($sollSelect)->where($sollWhere);

		$this->pdoQuery->setQueryOnce($sollWhereOnce);
		$this->assertsame($sollWhereOnce, $this->pdoQuery->get());

		// beim 2ten Lauf wieder standard
		//$this->assertsame ('SELECT '.$sollSelect.' FROM '.$sollFrom.' WHERE '.$sollWhere, $this->pdoQuery->get());

		//der Query Once ist in dem Fall h�her priorisiert!
		$this->assertsame($sollWhereOnce, $this->pdoQuery->get());
	}

	/**
	 * @return void
	 */
	public function testGet_Default()
	{
		$sollFrom = 'db_info';
		$sollSelect = 'row1, row2';
		$sollWhere = 'id=42';

		$query = $this->pdoQuery->from($sollFrom)->select($sollSelect)->where($sollWhere);
		$this->assertsame('SELECT ' . $sollSelect . ' FROM ' . $sollFrom . ' WHERE ' . $sollWhere, $query->get());
	}

	/**
	 * @return void
	 */
	public function testGet_Default_withoutWhere()
	{
		$sollFrom = 'db_info';
		$sollSelect = 'row1, row2';

		$query = $this->pdoQuery->from($sollFrom)->select($sollSelect);
		$this->assertsame('SELECT ' . $sollSelect . ' FROM ' . $sollFrom, $query->get());
	}

	/**
	 * @return void
	 */
	public function testGet_distinct()
	{
		$sollFrom = 'db_info';
		$sollSelect = 'row1, row2';

		$query = $this->pdoQuery->distinct()->from($sollFrom)->select($sollSelect);
		$this->assertsame('SELECT DISTINCT ' . $sollSelect . ' FROM ' . $sollFrom, $query->get());
	}

	/**
	 * @return void
	 */
	public function testaddHaving()
	{
		$query = $this->pdoQuery->addHaving('unit', 'test');
		$this->assertsame('SELECT * HAVING unit = test', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testaddHaving_numeric()
	{
		$query = $this->pdoQuery->addHaving('unit', 5);
		$this->assertsame('SELECT * HAVING unit = 5', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testaddHaving_isnull()
	{
		$query = $this->pdoQuery->addHaving('unit', null);
		$this->assertsame('SELECT * HAVING unit = NULL', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));

		$query = $this->pdoQuery->addHaving('unit2', null, 'IS');
		$this->assertsame('SELECT * HAVING unit = NULL AND unit2 IS NULL', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testaddHaving_concat()
	{
		$query = $this->pdoQuery->addHaving('unit', null);
		$this->assertsame('SELECT * HAVING unit = NULL', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));

		$query = $this->pdoQuery->addHaving('unit2', 8, '=', 'OR');
		$this->assertsame('SELECT * HAVING unit = NULL OR unit2 = 8', $this->replaceBind($this->pdoQuery->getBindValues(), $this->pdoQuery->get()));
	}

	/**
	 * @return void
	 */
	public function testaddHaving_Exception()
	{
		try
		{
			$this->pdoQuery->addHaving('unit', array());
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

		$query = $this->pdoQuery->from($sollFrom)->select($sollSelect)->orderBy('order asc');
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

		$query = $this->pdoQuery->from($sollFrom)->select($sollSelect)->groupBy('order asc');
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

		$query = $this->pdoQuery->from($sollFrom)->select($sollSelect)->join($solljointable)->on($solljoinon);
		$this->assertsame('SELECT ' . $sollSelect . ' FROM ' . $sollFrom . ' LEFT JOIN ' . $solljointable . ' ON ' . $solljoinon, $query->get());

		$this->pdoQuery = new PDOQuery();
		$query = $this->pdoQuery->from($sollFrom)->select($sollSelect)->join($solljointable)->on($solljoinon)->join($solljointable)->on($solljoinon);
		$this->assertsame('SELECT ' . $sollSelect . ' FROM ' . $sollFrom . ' LEFT JOIN ' . $solljointable . ' ON ' . $solljoinon . ' ' . ' LEFT JOIN ' . $solljointable . ' ON ' . $solljoinon, $query->get());

		$this->pdoQuery = new PDOQuery();
		$query = $this->pdoQuery->from($sollFrom)->select($sollSelect)->join($solljointable)->on($solljoinon)->where(" a = b");

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

		$query = $this->pdoQuery->from($sollFrom)->select($sollSelect)->join($solljointable, 'RIGHT')->on($solljoinon);
		$this->assertsame('SELECT ' . $sollSelect . ' FROM ' . $sollFrom . ' RIGHT JOIN ' . $solljointable . ' ON ' . $solljoinon, $query->get());
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

		$query = $this->pdoQuery->from($sollFrom)->select($sollSelect)->leftjoin($solljointable)->on($solljoinon);
		$this->assertsame('SELECT ' . $sollSelect . ' FROM ' . $sollFrom . ' LEFT JOIN ' . $solljointable . ' ON ' . $solljoinon, $query->get());
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

		$query = $this->pdoQuery->from($sollFrom)->select($sollSelect)->innerjoin($solljointable)->on($solljoinon);
		$this->assertsame('SELECT ' . $sollSelect . ' FROM ' . $sollFrom . ' INNER JOIN ' . $solljointable . ' ON ' . $solljoinon, $query->get());
	}

	/**
	 * @return void
	 */
	public function testLimit()
	{
		$this->pdoQuery->limit(5);
		$limit = $this->readAttribute($this->pdoQuery, 'limit');
		$this->assertSame(5, $limit[0]);
		$this->assertSame(1, count($limit));

		$this->pdoQuery->limit(5, 10);
		$limit = $this->readAttribute($this->pdoQuery, 'limit');
		$this->assertSame(5, $limit[0]);
		$this->assertSame(10, $limit[1]);

		$this->assertSame('SELECT * LIMIT 5, 10', $this->pdoQuery->get());
	}

	/**
	 * Tests für Aufbau komplexer Query mit dem Querybuilder.
	 * Cases/Expects wurden aus einem echten System (Favesync) verwendet.
	 *
	 * @return void
	 */
	public function testComplexQueryBuilding1()
	{
		$inQuery = new PDOQuery();
		$inQuery->select('fav_id')->from('test_table1')
			->join('join_table3')->on("fel_idfave = fav_id")
			->addWhere('fav_public', '0')
			->addWhere('fel_new', 0)
			->addWhere('fel_idfave_fellow', 222);

		$query = $this->pdoQuery
			->select('*')->from('test_table2')
			->join('join_table1')->on('fav_iduser = usr_id')
			->join('join_table2')->on('(rft_idfave = fav_id AND rft_active = 1)');

		$inTags = array(10, 15, 22, 2555, 13);

		$query->addWhere('usr_confirm', 1);
		$query->addWhere('fav_deleted', 0);
		$query->addWhere('fav_tmpclone', 0);
		$query->addWhere('fav_invite', 0);
		$query->addWhere('usr_id', 111, '!=');
		$query->addWhere('rft_idtag', $inTags);
		$query->openClosure();
		$query->addWhere('fav_public', 1);
		$query->openClosure();
		$query->addWhere('fav_public', 0, '=', 'OR');
		$query->innerStatement('fav_id', $inQuery);
		$query->closeClosure();
		$query->closeClosure();

		// $query->where( implode(' AND ', $where) );
		$query->groupBy('fav_id');

		$statement = $query->get();
		$statement = preg_replace('/(:fieldName[a-f0-9]{13})/i', '?', $statement);

		$expects = '
		SELECT * FROM test_table2
			LEFT JOIN join_table1 ON fav_iduser = usr_id
			LEFT JOIN join_table2 ON (rft_idfave = fav_id AND rft_active = 1)
		WHERE usr_confirm = ?
			AND fav_deleted = ?
			AND fav_tmpclone = ?
			AND fav_invite = ?
			AND usr_id != ?
			AND rft_idtag IN (?,?,?,?,?)
			AND (
				fav_public = ?
				OR (
					fav_public = ?
					AND fav_id IN (
						SELECT fav_id FROM test_table1
							LEFT JOIN join_table3 ON fel_idfave = fav_id
						WHERE fav_public = ?
							AND fel_new = ?
							AND fel_idfave_fellow = ?
					)
				)
			)
		GROUP BY fav_id
		';

		$expects = $this->cleanStatement($expects);
		$statement = $this->cleanStatement($statement);

		$this->assertSame($expects, $statement);
	}

	/**
	 * Tests für Aufbau komplexer Query mit dem Querybuilder.
	 * Case identisch mit dem Vortest, jedoch werden diesmal
	 * die query-Methoden in anderer Reihenfolge aufgerufen
	 *
	 * @return void
	 */
	public function testComplexQueryBuilding2()
	{
		$inQuery = new PDOQuery();
		$inQuery->select('fav_id')->from('test_table1')
			->addWhere('fav_public', '0')
			->addWhere('fel_new', 0)
			->addWhere('fel_idfave_fellow', 222)
			->join('join_table3')->on("fel_idfave = fav_id");

		$query = $this->pdoQuery;
		$query->select('*');
		$query->join('join_table1');
		$query->from('test_table2');
		$query->on('fav_iduser = usr_id');

		$inTags = array(10, 15, 22, 2555, 13);

		$query->groupBy('fav_id');

		$query->addWhere('usr_confirm', 1);
		$query->addWhere('fav_deleted', 0);
		$query->addWhere('fav_tmpclone', 0);
		$query->addWhere('fav_invite', 0);
		$query->addWhere('usr_id', 111, '!=');
		$query->addWhere('rft_idtag', $inTags);
		$query->openClosure();
		$query->addWhere('fav_public', 1);
		$query->openClosure();
		$query->addWhere('fav_public', 0, '=', 'OR');
		$query->innerStatement('fav_id', $inQuery);
		$query->closeClosure();
		$query->closeClosure();

		$query->join('join_table2')->on('(rft_idfave = fav_id AND rft_active = 1)');

		$statement = $query->get();

		$expects = '
		SELECT * FROM test_table2
			LEFT JOIN join_table1 ON fav_iduser = usr_id
			LEFT JOIN join_table2 ON (rft_idfave = fav_id AND rft_active = 1)
		WHERE usr_confirm = ?
			AND fav_deleted = ?
			AND fav_tmpclone = ?
			AND fav_invite = ?
			AND usr_id != ?
			AND rft_idtag IN (?,?,?,?,?)
			AND (
				fav_public = ?
				OR (
					fav_public = ?
					AND fav_id IN (
						SELECT fav_id FROM test_table1
							LEFT JOIN join_table3 ON fel_idfave = fav_id
						WHERE fav_public = ?
							AND fel_new = ?
							AND fel_idfave_fellow = ?
					)
				)
			)
		GROUP BY fav_id
		';

		$expects = $this->cleanStatement($expects);
		$statement = $this->cleanStatement($statement);

		$this->assertSame($expects, $statement);
	}

	private function cleanStatement($stmt)
	{
		$stmt = preg_replace('/(:fieldName[a-f0-9]{13})/i', '?', $stmt);
		$stmt = preg_replace('/([\r\t])/is', '', $stmt);
		$stmt = preg_replace('/([\n])/is', ' ', $stmt);
		$stmt = preg_replace('/(\s\s)/is', ' ', $stmt);
		$stmt = preg_replace('/(\s\))/is', ')', $stmt);
		$stmt = preg_replace('/(\(\s)/is', '(', $stmt);
		$stmt = trim($stmt);
		return $stmt;
	}

	/**
	 * @return void
	 */
	protected function setUp()
	{
		$this->pdoQuery = new PDOQuery();
	}

	/**
	 * @return void
	 */
	protected function tearDown()
	{
		unset($this->pdoQuery);
		//unset($this->db);
	}
}