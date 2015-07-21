<?php

namespace jamwork\common;

/**
 * Class Event
 *
 * @category Jamwork
 * @package  jamwork\common
 * @author   Cindy Paulitz <cindy@dreiwerken.de>
 */
class Event
{

	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var mixed
	 */
	protected $context;
	/**
	 * @var mixed
	 */
	protected $info;
	/**
	 * @var bool
	 */
	protected $canceled = false;

	/**
	 * @param string $name
	 * @param null   $context
	 * @param null   $info
	 */
	public function __construct($name, $context = null, $info = null)
	{
		$this->name = $name;
		$this->context = $context;
		$this->info = $info;
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return null
	 */
	public function getContext()
	{
		return $this->context;
	}

	/**
	 * @return null
	 */
	public function getInfo()
	{
		return $this->info;
	}

	/**
	 * @return bool
	 */
	public function isCanceled()
	{
		return $this->canceled;
	}

	/**
	 * @return bool
	 */
	public function cancel()
	{
		return $this->canceled = true;
	}

	/**
	 * @param mixed $context
	 * @return void
	 *
	 */
	public function setContext($context)
	{
		$this->context = $context;
	}

	/**
	 * @param mixed $info
	 * @return void
	 */
	public function setInfo($info)
	{
		$this->info = $info;
	}

}