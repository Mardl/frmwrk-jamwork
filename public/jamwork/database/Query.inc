<?php

namespace jamwork\database;

interface Query
{
	public function from($table);
	public function select($fields);
	public function where($clause);
	public function orderBy($order);
	public function join($order);
	public function on($order);
	public function setQueryOnce($queryString);
	public function get();
}