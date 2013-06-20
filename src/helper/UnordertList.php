<?php

namespace jamwork\helper;

class UnordertList
{
	private $items = array();
	private $ulClass = array();
	private $itemCount = 0;
	
	public function addItem($item, $attr='')
	{
		$this->items[ $this->itemCount ] = array(
			'text' => $item
		);
		
		if(is_array($attr))
		{
			foreach($attr as $key => $value)
			{
				$this->items[ $this->itemCount ][$key] = $value;
			}
		}
		else
		{
			$this->items[ $this->itemCount ]['class'] = $attr;
		}
		
		$this->itemCount++;
		
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
			$strOut .= '<li'.$this->getItemData($item, 'id').$this->getItemData($item, 'class').'>'.$item['text'].'</li>';
		}
		return $strOut;
	}
	
	private function getItemData($item, $key, $label='')
	{
		$label = empty($label) ? $key : $label;
		
		if(isset($item[$key]) && !empty($item[$key]))
		{
			return ' '.$label.'="'.$item[$key].'"';
		}
		return '';
	}
}