<?php

namespace jamwork\template;

interface Script
{
	public function add($file);
	public function remove($file);
	public function flush();
	public function setCacheDir($dir);
}
