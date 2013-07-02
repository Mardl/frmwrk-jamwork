<?php

namespace jamwork\form;

class Form
{

	protected $id = '';
	protected $name = '';
	protected $method = '';
	protected $action = '';
	protected $enctype = '';
	protected $classes = array();

	public function name($name)
	{
		$this->name = $name;

		return $this;
	}

	public function getName()
	{
		return $this->name;
	}

	public function id($id)
	{
		$this->id = $id;

		return $this;
	}

	public function getId()
	{
		return $this->id;
	}

	public function method($method)
	{
		$this->method = $method;

		return $this;
	}

	public function getMethod()
	{
		return $this->method;
	}

	public function action($action)
	{
		$this->action = $action;

		return $this;
	}

	public function getAction()
	{
		return $this->action;
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

	public function enctype($enctype)
	{
		$this->enctype = $enctype;

		return $this;
	}

	public function getEnctype()
	{
		return $this->enctype;
	}
}
