<?php

namespace jamwork\database;

interface Query
{
	/**
	 * @abstract
	 * @param $table
	 * @return Query
	 */
	public function from($table);

	/**
	 * @abstract
	 * @param $fields
	 * @return Query
	 */
	public function select($fields);

	/**
	 * @abstract
	 * @param $clause
	 * @return Query
	 */
	public function where($clause);

	/**
	 * @abstract
	 * @param $order
	 * @return Query
	 */
	public function orderBy($order);

	/**
	 * @abstract
	 * @param $order
	 * @return Query
	 */
	public function join($order);

	/**
	 * @abstract
	 * @param $joinOn
	 * @return Query
	 */
	public function on($joinOn);

	/**
	 * @abstract
	 * @param $queryString
	 * @return Query
	 */
	public function setQueryOnce($queryString);

	/**
	 * @abstract
	 * @return Query
	 */
	public function get();


	/**
	 * @abstract
	 * @param $field
	 * @param $value
	 * @param string $op
	 * @param string $concat
	 * @return Query
	 */
	public function addWhere($field, $value, $op = '=', $concat = 'AND');


	/**
	 * @abstract
	 * @param $field
	 * @param string $op
	 * @param string $concat
	 * @return Query
	 */
	public function addWhereIsNull($field, $op = 'IS', $concat = 'AND');

	/**
	 * @abstract
	 * @param $field
	 * @param $value
	 * @param string $phraseOrder
	 * @param string $concat
	 * @return Query
	 */
	public function addWhereLike($field, $value, $phraseOrder = '%%%s%%', $concat = 'AND');

	/**
	 * @abstract
	 * @param $field
	 * @param $value
	 * @param string $op
	 * @param string $concat
	 * @return Query
	 */
	public function addHaving($field, $value, $op = '=', $concat = 'AND');

	/**
	 * @abstract
	 * @param $field
	 * @param array $values
	 * @return Query
	 */
	public function in($field, array $values);

	/**
	 * @abstract
	 * @param $groupby
	 * @return Query
	 */
	public function groupBy($groupby);

	/**
	 * @abstract
	 * @param $offset
	 * @param null $limit
	 * @return Query
	 */
	public function limit($offset, $limit=null);

	/**
	 * @abstract
	 * @param $join
	 * @return Query
	 */
	public function innerJoin($join);

	/**
	 * @abstract
	 * @param $join
	 * @return Query
	 */
	public function leftJoin($join);
}