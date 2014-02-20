<?php

namespace jamwork\database;

/**
 * Interface Query
 *
 * @category Jamwork
 * @package  Jamwork\database
 * @author   Martin Eisenf�hrer <martin@dreiwerken.de>
 */
interface Query
{

	/**
	 * @param string $table
	 * @return Query
	 */
	public function from($table);

	/**
	 * @param string $fields
	 * @return Query
	 */
	public function select($fields);

	/**
	 * @param string $clause
	 * @return Query
	 */
	public function where($clause);

	/**
	 * @param string $order
	 * @return Query
	 */
	public function orderBy($order);

	/**
	 * @param string $order
	 * @return Query
	 */
	public function join($order);

	/**
	 * @param string $joinOn
	 * @return Query
	 */
	public function on($joinOn);

	/**
	 * @param string $queryString
	 * @return Query
	 */
	public function setQueryOnce($queryString);

	/**
	 * @return Query
	 */
	public function get();

	/**
	 * @param bool $distinct
	 * @return void
	 */
	public function distinct($distinct = true);


	/**
	 * @param string $field
	 * @param string $value
	 * @param string $op
	 * @param string $concat
	 * @return Query
	 */
	public function addWhere($field, $value, $op = '=', $concat = 'AND');


	/**
	 * @param string $field
	 * @param string $op
	 * @param string $concat
	 * @return Query
	 */
	public function addWhereIsNull($field, $op = 'IS', $concat = 'AND');

	/**
	 * @param string $field
	 * @param string $value
	 * @param string $phraseOrder
	 * @param string $concat
	 * @return Query
	 */
	public function addWhereLike($field, $value, $phraseOrder = '%%%s%%', $concat = 'AND');

	/**
	 * @param string $field
	 * @param string $value
	 * @param string $op
	 * @param string $concat
	 * @return Query
	 */
	public function addHaving($field, $value, $op = '=', $concat = 'AND');

	/**
	 * @param string $field
	 * @param string $valueMin
	 * @param string $valueMax
	 * @param string $concat
	 * @return mixed
	 */
	public function addWhereBetween($field, $valueMin, $valueMax, $concat = 'AND');

	/**
	 * @param string $field
	 * @param array  $values
	 * @return Query
	 */
	public function in($field, array $values);

	/**
	 * @param string $groupby
	 * @return Query
	 */
	public function groupBy($groupby);

	/**
	 * @param int $offset
	 * @param int $limit
	 * @return Query
	 */
	public function limit($offset, $limit = null);

	/**
	 * @param string $join
	 * @return Query
	 */
	public function innerJoin($join);

	/**
	 * @param string $join
	 * @return Query
	 */
	public function leftJoin($join);

	/**
	 * @param string $field
	 * @param mixed  $value
	 * @param bool   $positiv
	 * @return mixed
	 */
	public function innerStatement($field, $value, $positiv = true);

	/**
	 * @return string
	 */
	public function getPreparedStatement();

}