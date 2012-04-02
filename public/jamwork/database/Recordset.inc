<?php

namespace jamwork\database;

interface Recordset
{
	public function execute(Query $query);
	public function count();
	public function get();
}
