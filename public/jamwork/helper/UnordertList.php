<?php

namespace jamwork\helper;

class UnordertList
{
	private $items = array();
	private $ulClass = array();
	
	public function addItem($item, $class='')
	{
		$this->items[] = array(
			'text' => $item,
			'class' => $class
		);
		return $this;
	}
	
	public function addClass($cls)
	{
		$this->ulClass[] = $cls;
	}
	
	private function getUlClass()
	{
		$cls = implode(' ', $this->ulClass);
		if(empty($cls))
		{
			return '';
		}
		return ' class="'.$cls.'"';
	}
	
	public function hasItems()
	{
		return !empty($this->items);
	}
	
	public function create()
	{
		if(!$this->hasItems())
		{
			return '';
		}
		return '<ul'.$this->getUlClass().'>'.$this->getItems().'</ul>';
	}
	
	public function __toString()
	{
		return $this->create();
	}
	
	private function getItems()
	{
		$strOut = '';
		foreach($this->items as $item)
		{
			$strOut .= '<li'.$this->getClass($item).'>'.$item['text'].'</li>';
		}
		return $strOut;
	}
	
	private function getClass($item)
	{
		if(!empty($item['class']))
		{
			return ' class="'.$item['class'].'"';
		}
		return '';
	}
}