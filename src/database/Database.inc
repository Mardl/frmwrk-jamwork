<?php

namespace jamwork\database;

/**
 * interface Database
 *
 * @category Jamwork
 * @package  Jamwork\database
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
interface Database
{

	/**
	 * @param string $host
	 * @param string $user
	 * @param string $pwd
	 * @param string $name
	 */
	public function __construct($host, $user, $pwd, $name);

	/**
	 * @return Query
	 */
	public function newQuery();


	/**
	 * @return Recordset
	 */
	public function newRecordSet();

	/**
	 * @param string $tableName
	 * @param array  $recordSet
	 * @return Database
	 */
	public function update($tableName, array $recordSet);

	/**
	 * @param string $tableName
	 * @param array  $recordSet
	 * @return Database
	 */
	public function insert($tableName, array $recordSet);

	/**
	 * @param string $tableName
	 * @param array  $recordSet
	 * @return Database
	 */
	public function delete($tableName, array $recordSet);

	/**
	 * @return Database
	 */
	public function startTransaction();

	/**
	 * @return Database
	 */
	public function commit();

	/**
	 * @return Database
	 */
	public function rollback();
}