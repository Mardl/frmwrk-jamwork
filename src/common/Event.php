<?php

namespace jamwork\common;

class Event
{

	protected $name;
	protected $context;
	protected $info;
	protected $canceled = false;

	public function __construct($name, $context = null, $info = null)
	{
		$this->name = $name;
		$this->context = $context;
		$this->info = $info;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getContext()
	{
		return $this->context;
	}

	public function getInfo()
	{
		return $this->info;
	}

	public function isCanceled()
	{
		return $this->canceled;
	}

	public function cancel()
	{
		return $this->canceled = true;
	}

}