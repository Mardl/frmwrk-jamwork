<?php

namespace jamwork\controller;

class DateTimeConverter
{
	const MINUTE 	= 60;
	const HOUR 		= 3600;
	const DAY 		= 86400;
	const WEEK 		= 604800;
	const MONTH 	= 2628000;
	const YEAR 		= 31536000;
	
	private $datetime = '';
	
	private $date = '';
	private $time = '';
	
	private $timeArr = array();
	private $dateArr = array();
	
	private $fallback = 0;
	private $offset = 0;
	
	static public $NOW = 0;
	
	public function __construct($datetime)
	{
		$this->datetime = $datetime;
		$this->fallback = time();
		$this->analyze();
	}
	
	public function __toString()
	{
		return $this->get('Y-m-d H:i:s');
	}
	
	public function setOffset($time)
	{
		$this->offset = $time;
	}
	
	public function addFallback($time)
	{
		$this->fallback += $time;
	}
		
	private function analyze()
	{
		if(empty($this->datetime))
		{
			return;
		}
		
		$datetime = explode(' ', $this->datetime);
		$this->date = $datetime[0];
		$this->time = isset($datetime[1]) ? $datetime[1] : '';
		
		$this->analyzeDate();
		$this->analyzeTime();
	}
	
	private function analyzeDate()
	{
		if(strpos($this->date, '-'))
		{
			$this->explode('-', array('Y', 'm', 'd'));
			return;
		}
		if(strpos($this->date, '.'))
		{
			$this->explode('.', array('d', 'm', 'Y'));
			return;
		}
		if(strpos($this->date, '/'))
		{
			$this->explode('/', array('d', 'm', 'Y'));
			return;
		}
	}
	
	private function explode($dlm, array $format)
	{
		$datum = array();
		
		$date = explode($dlm, $this->date);
		foreach($format as $key => $typ)
		{
			$datum[$typ] = $date[$key];
		}
		
		$this->dateArr = $datum;
	}
	
	private function analyzeTime()
	{
		$time = explode(':', $this->time);
		
		$this->timeArr['h'] = !empty($time[0]) ? $time[0] : 0;
		$this->timeArr['i'] = isset($time[1]) ? $time[1] : 0;
		$this->timeArr['s'] = isset($time[2]) ? $time[2] : 0;
	}
	
	private function getTimestamp()
	{
		if(empty($this->timeArr))
		{
			return $this->fallback;
		}
		return mktime($this->timeArr['h'], $this->timeArr['i'], $this->timeArr['s'], $this->dateArr['m'], $this->dateArr['d'], $this->dateArr['Y']);
	}
	
	public function get($format)
	{
		return date($format, $this->getTimestamp() + $this->offset);
	}
}