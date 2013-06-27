<?php

namespace jamwork\database;

interface Update
{

	/**
	 * @abstract
	 * @param $table
	 * @return Query
	 */
	public function update($table);

	/**
	 * @abstract
	 * @param $clause
	 * @return Query
	 */
	public function where($clause);


	/**
	 * @abstract
	 * @return Query
	 */
	public function get();


	/**
	 * @abstract
	 * @param        $field
	 * @param        $value
	 * @param string $op
	 * @param string $concat
	 * @return Query
	 */
	public function addWhere($field, $value, $op = '=', $concat = 'AND');


	/**
	 * @abstract
	 * @param        $field
	 * @param string $op
	 * @param string $concat
	 * @return Query
	 */
	public function addWhereIsNull($field, $op = 'IS', $concat = 'AND');

	/**
	 * @abstract
	 * @param        $field
	 * @param        $value
	 * @param string $phraseOrder
	 * @param string $concat
	 * @return Query
	 */
	public function addWhereLike($field, $value, $phraseOrder = '%%%s%%', $concat = 'AND');


	/**
	 * @abstract
	 * @param       $field
	 * @param array $values
	 * @return Query
	 */
	public function in($field, array $values);

}