<?php

namespace jamwork\helper;

class Anchor
{
	protected $classPrefix = '';
	private $hyperlink = null;
	private $text = '';
	private $title = '';
	private $external = false;
	private $textSet = '';
	private $classes = array();
	
	public function __construct(Hyperlink $hyperlink)
	{
		$this->hyperlink = $hyperlink;
	}
	
	public function text($text)
	{
		$this->text = $text;
		return $this;
	}
	
	public function title($title)
	{
		$this->title = $title;
		return $this;
	}
	
	public function button()
	{
		$this->addClass($this->classPrefix.'button');
		return $this;
	}
	
	public function external()
	{
		$this->external = true;
		return $this;
	}
	
	public function icon($icon)
	{
		$this->addClass($this->classPrefix.'icon');
		$this->addClass($this->classPrefix.'icon-'.$icon);
		return $this;
	}
	
	public function textSet($text)
	{
		$this->textSet = $text;
		return $this;
	}
	
	public function noText()
	{
		$this->addClass($this->classPrefix.'notext');
		return $this;
	}
	
	public function addClass($cls)
	{
		$this->classes[] = $cls;
		return $this;
	}
	
	public function create()
	{
		$strOut = '<a href="'.$this->getHref().'"'.$this->getClasses().$this->getTitle().$this->getExternal().'>'.$this->getText().'</a>';
		return $strOut;
	}
	
	private function getTitle()
	{
		if(!empty($this->title))
		{
			return ' title="'.$this->title.'"';
		}
		return '';
	}
	
	private function getExternal()
	{
		if($this->external)
		{
			return ' target="_blank"';
		}
		return '';
	}
	
	public function __toString()
	{
		return $this->create();
	}
	
	private function getText()
	{
		if(empty($this->text))
		{
			return '';
		}
		if(!empty($this->textSet))
		{
			return '<span class="'.$this->classPrefix.'text '.$this->classPrefix.'text-'.$this->textSet.'"><span class="anchor-text">'.$this->text.'</span></span>';
		}
		return '<span class="anchor-text">'.$this->text.'</span>';
	}
	
	private function getHref()
	{
		return $this->hyperlink->getUrl();
	}
	
	private function getClasses()
	{
		if(empty($this->classes))
		{
			return '';
		}
		return ' class="'.implode(' ', $this->classes).'"';
	}
}