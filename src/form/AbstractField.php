<?php

namespace jamwork\form;

abstract class AbstractField implements Field
{
	protected $required = false;
	protected $labelRight = false;
	protected $id = '';
	protected $name = '';
	protected $value = '';
	protected $label = '';
	protected $classes = array();
	protected $marker = '';
	protected $dataAttr = array();
	protected $placeholder = '';
	
	abstract public function getFieldType(); // Muss den Typ des Field zurückliefern als string

	public function __construct($marker='')
	{
		$this->marker = $marker;
	}
	
	public function required()
	{
		$this->required = true;
		return $this;
	}
	
	public function label($label)
	{
		$this->label = $label;
		return $this;
	}
	
	public function name($name)
	{
		$this->name = $name;
		return $this;
	}
	
	public function value($value)
	{
		$this->value = $value;
		return $this;
	}
	
	public function id($id)
	{
		$this->id = $id;
		return $this;
	}
	
	public function labelRight()
	{
		$this->labelRight = true;
		return $this;
	}
	
	public function addClass($class)
	{
		$this->classes[] = $class;
		return $this;
	}
		
	public function getType()
	{
		return $this->getFieldType();//$this::TYPE;
	}
	public function isRequired()
	{
		return $this->required;
	}
	public function getLabel()
	{
		return $this->label;
	}
	public function getName()
	{
		return $this->name;
	}
	public function getValue()
	{
		return $this->value;
	}
	public function getId()
	{
		return $this->id;
	}
	public function hasLabelRight()
	{
		return $this->labelRight;
	}
	public function getClasses()
	{
		return implode(' ', $this->classes);
	}
	
	public function getMarker()
	{
		return '#'.$this->marker.'#';
	}
	
	public function getDataAttr()
	{
		if(empty($this->dataAttr))
		{
			return '';
		}
		
		$data = array();
		foreach($this->dataAttr as $key => $value)
		{
			$data[] = 'data-'.$key.'="'.$value.'"';
		}
		return ' '.implode(' ', $data);
	}
	
	public function dataAttr($key, $value)
	{
		$this->dataAttr[$key] = $value;
		return $this;
	}
	
	public function placeholder($text)
	{
		$this->placeholder = $text;
		return $this;
	}
	
	public function getPlaceholder()
	{
		return $this->placeholder;
	}
}