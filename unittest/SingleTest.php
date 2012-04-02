<?php

namespace unittest;

class SingleTest
{
	private $suite = null;
	
	public function __construct($test)
	{
		$this->suite  = new \PHPUnit_Framework_TestSuite($test.'Test');
	}
	
	public function getSuite()
	{
		return $this->suite;
	}

}
