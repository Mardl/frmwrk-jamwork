<?php

namespace jamwork\table;

class TableBody
{

	private $classes = array();
	private $rows = array();

	public function addClass($cls)
	{
		$this->classes[] = $cls;

		return $this;
	}

	public function row()
	{
		$row = new TableRow();
		$this->rows[] = $row;

		return $row;
	}

	public function getClasses()
	{
		return implode(' ', $this->classes);
	}

	public function getRows()
	{
		return $this->rows;
	}

	public function getLastRow()
	{
		$rows = count($this->rows);
		$lastRow = $rows - 1;

		return $this->getRow($lastRow);
	}

	public function getRow($i)
	{
		if ($this->issetRow($i))
		{
			return $this->rows[$i];
		}

		return false;
	}

	public function issetRow($i)
	{
		return isset($this->rows[$i]);
	}

	public function getFirstRow()
	{
		return $this->getRow(0);
	}
}
