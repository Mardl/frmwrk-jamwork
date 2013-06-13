<?php

namespace jamwork\form;

class FormFactory
{
	private $array = array();
	private $formOutput = null;
	private $method = '';
	private $action = '';
	private $form = false;
	private $legend = '';
	private $classes = array();
	private $content = '';
	private $markerPrefix = 0;
	private $counter = 0;
	private $ownMarker = '';
	protected $dataAttr = array();
	
	public function __construct(FormOutput $formOutput, $markerPrefix=0, $ownMarker='')
	{
		$this->formOutput = $formOutput;
		$this->markerPrefix = $markerPrefix;
		$this->ownMarker = $ownMarker;
	}
	public function __toString()
	{
		return $this->create();
	}
	
	public function fieldset()
	{
		$field = new FormFactory($this->formOutput, $this->nextMarkerPrefix(), $this->markerPrefix.'-'.$this->counter);
		$this->array[ $this->markerPrefix.'-'.$this->counter ] = $field;
		return $field;
	}
	
	private function nextMarkerPrefix()
	{
		return ($this->markerPrefix + $this->nextCounter());
	}
	
	private function nextCounter()
	{
		$this->counter++;
		return $this->counter;
	}
	
	private function nextMarker()
	{
		$marker = $this->markerPrefix.'-'.$this->nextCounter();
		return $marker;
	}
	
	public function getMarker()
	{
		return '#'.$this->ownMarker.'#';
	}
	
	public function content($cnt)
	{
		$this->content = $cnt;
	}
	
	public function addClass($class)
	{
		$this->classes[] = $class;
		return $this;
	}
	
	public function getClasses()
	{
		return implode(' ', $this->classes);
	}
	
	public function legend($legend)
	{
		$this->legend = $legend;
		return $this;
	}
	
	public function getLegend()
	{
		return $this->legend;
	}
	
	public function issetLegend()
	{
		return !empty($this->legend);
	}
	
	public function textfield()
	{
		$marker = $this->nextMarker();
		$field = new Textfield($marker);
		$this->array[$marker] = $field;
		return $field;
	}
	
	public function textarea()
	{
		$marker = $this->nextMarker();
		$field = new Textarea($marker);
		$this->array[$marker] = $field;
		return $field;
	}

	/**
	 * @return Select
	 */
	public function select()
	{
		$marker = $this->nextMarker();
		$field = new Select($marker);
		$this->array[$marker] = $field;
		return $field;
	}
	
	public function radiobutton()
	{
		$marker = $this->nextMarker();
		$field = new Radiobutton($marker);
		$this->array[$marker] = $field;
		return $field;
	}
	
	public function password()
	{
		$marker = $this->nextMarker();
		$field = new Password($marker);
		$this->array[$marker] = $field;
		return $field;
	}
	
	public function hidden()
	{
		$marker = $this->nextMarker();
		$field = new Hidden($marker);
		$this->array[$marker] = $field;
		return $field;
	}
	
	public function checkbox()
	{
		$marker = $this->nextMarker();
		$field = new Checkbox($marker);
		$this->array[$marker] = $field;
		return $field;
	}
	
	public function button()
	{
		$marker = $this->nextMarker();
		$field = new Button($marker);
		$this->array[$marker] = $field;
		return $field;
	}
	
	public function ptag()
	{
		$marker = $this->nextMarker();
		$field = new Ptag($marker);
		$this->array[$marker] = $field;
		return $field;
	}
	
	public function filefield()
	{
		$marker = $this->nextMarker();
		$field = new FileField($marker);
		$this->array[$marker] = $field;
		return $field;
	}
	
	public function trenner()
	{
		$marker = $this->nextMarker();
		$field = new Trenner($marker);
		$this->array[$marker] = $field;
		return $field;
	}
	
	public function form()
	{
		$field = new Form();
		$this->form = $field;
		return $field;
	}
	
	public function create()
	{
		$str = $this->formOutput->generate( $this->generate(), $this->form );
		return $str;
	}
	
	public function generate()
	{
		if(!empty($this->content))
		{
			return $this->replaceMarker($this->content, $this->array);
		}
		
		$str = '';
		foreach ( $this->array as $field )
		{
			$str .= $this->generateStr($field);
		}
		return $str;
	}
	
	private function replaceMarker($cnt, $arr)
	{
		if(!empty($arr))
		{
			foreach ( $arr as $marker => $field )
			{
				if ( $field instanceof FormFactory )
				{
					$cnt = str_replace('#'.$marker.'#', '', $cnt);
					$cnt = $this->replaceMarker($cnt, $field->getArray());
					continue;
				}
				$cnt = str_replace('#'.$marker.'#', $this->generateStr($field), $cnt);
			}
		}
		return $cnt;
	}
	
	public function generateStr($field)
	{
		if ( $field instanceof FormFactory )
		{
			return $this->formOutput->generateFieldset($field);
		}
		return $this->formOutput->generateFormField($field);
	}
	
	public function getArray()
	{
		return $this->array;
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
}