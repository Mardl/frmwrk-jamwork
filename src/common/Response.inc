<?php

namespace jamwork\common;

interface Response
{
	public function setStatus($status);
	public function addHeader($name, $value);
	public function write($data);
	public function flush($dump = false);
	public function getBody();
	public function setBody($body);
	public function getHeader();
	public function unsetHeader($name);
	public function getStatus();
	public function addReturn(Command $obj, $data);
}
