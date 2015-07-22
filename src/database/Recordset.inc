<?php

namespace jamwork\database;

/**
 * interface Recordset
 *
 * @category Jamwork
 * @package  Jamwork\database
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
interface Recordset
{

	/**
	 * @param Query $query
	 * @return Recordset
	 */
	public function execute(Query $query);

	/**
	 * @return integer|boolean
	 */
	public function count();

	/**
	 * @return mixed
	 */
	public function get();

	/**
	 * @return mixed
	 */
	public function isSuccessful();
}
