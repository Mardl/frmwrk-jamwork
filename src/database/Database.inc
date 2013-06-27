<?php

namespace jamwork\database;

interface Database
{

	public function __construct($host, $user, $pwd, $name);

	/**
	 * @abstract
	 * @return Query
	 */
	public function newQuery();


	/**
	 * @abstract
	 * @return Recordset
	 */
	public function newRecordSet();

	public function update($tableName, array $recordSet);

	public function insert($tableName, array $recordSet);

	public function delete($tableName, array $recordSet);

	public function startTransaction();

	public function commit();

	public function rollback();
}