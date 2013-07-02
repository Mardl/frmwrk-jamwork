<?php

namespace jamwork\table;

class Table
{

	private $thead = null;
	private $tbody = null;
	private $tfoot = null;

	private $output = null;

	public function __construct(TableOutput $output)
	{
		$this->output = $output;
	}

	public function __clone()
	{
		return new Table($this->getTableOutput());
	}

	public function getTableOutput()
	{
		return clone $this->output;
	}

	public function id($id)
	{
		$this->output->setId($id);

		return $this;
	}

	public function addClass($cls)
	{
		$this->output->addClass($cls);

		return $this;
	}

	public function thead()
	{
		if (empty($this->thead))
		{
			$this->thead = new TableBody();
			$this->output->setHead($this->thead);
		}

		return $this->thead;
	}

	public function tfoot()
	{
		if (empty($this->tfoot))
		{
			$this->tfoot = new TableBody();
			$this->output->setFoot($this->tfoot);
		}

		return $this->tfoot;
	}

	public function tbody()
	{
		if (empty($this->tbody))
		{
			$this->tbody = new TableBody();
			$this->output->setBody($this->tbody);
		}

		return $this->tbody;
	}

	public function create()
	{
		return $this->output->generate();
	}

	public function __toString()
	{
		return $this->create();
	}
}