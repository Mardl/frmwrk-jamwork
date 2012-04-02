<?php

namespace jamwork\common;

interface Session 
{
	public function get($name);
	public function has($name);
	public function set($name, $value);
	public function delete($name);
	public function destroy();
	public function getId();
}