<?php

namespace jamwork\database;

interface Recordset
{

	/**
	 * @abstract
	 * @param Query $query
	 * @return Recordset
	 */
	public function execute(Query $query);

	/**
	 * @abstract
	 * @return integer|boolean
	 */
	public function count();

	public function get();

	public function isSuccessful();
}
