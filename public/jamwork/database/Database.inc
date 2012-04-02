<?php

namespace jamwork\database;

interface Database
{
	public function __construct($host, $user, $pwd, $name);
	public function newQuery();
	public function newRecordSet();
	public function update($tableName, array $recordSet);
	public function insert($tableName, array $recordSet);
	public function delete($tableName, array $recordSet);
}