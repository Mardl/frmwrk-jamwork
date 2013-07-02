<?php

namespace jamwork\table;

class TableRow
{

	private $classes = array();
	private $cells = array();

	public function addCell($data, $cls = '', $colspan = 0)
	{
		$this->cells[] = array('data' => $data, 'class' => $cls, 'colspan' => $colspan);

		return $this;
	}

	public function addClass($cls)
	{
		$this->classes[] = $cls;

		return $this;
	}

	public function getClasses()
	{
		return implode(' ', $this->classes);
	}

	public function getCells()
	{
		return $this->cells;
	}

	public function getCell($i)
	{
		if ($this->issetCell($i))
		{
			return $this->cells[$i];
		}

		return false;
	}

	public function issetCell($i)
	{
		return isset($this->cells[$i]);
	}

	public function getLastCell()
	{
		$cells = count($this->cells);
		$lastCell = $cells - 1;

		return $this->getCell($lastCell);
	}

	public function getFirstCell()
	{
		return $this->getCell(0);
	}
}