<?php

namespace jamwork\table;

interface TableOutput
{

	public function generate();

	public function setHead(TableBody $head);

	public function setBody(TableBody $body);

	public function setFoot(TableBody $foot);
}