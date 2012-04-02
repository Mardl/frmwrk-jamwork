<?php

namespace jamwork\template;

interface Section
{
	public function append($data);
	public function prepend($data);
	public function flush();
}
