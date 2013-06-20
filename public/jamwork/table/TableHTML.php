<?php

namespace jamwork\table;

class TableHTML implements TableOutput
{
	private $head = null;
	private $body = null;
	private $foot = null;
	private $id = '';
	private $classes = array();
	
	private $strOut = '';
	private $maxCells = 0;
	private $dataField = '';
	
	public function __clone()
	{
		return new TableHTML();
	}
	
	public function generate()
	{
		$this->strOut = '';
		$this->create();
		return $this->strOut;
	}

	public function setHead(TableBody $head)
	{
		$this->head = $head;
	}
	
	public function setBody(TableBody $body)
	{
		$this->body = $body;
	}
	
	public function setFoot(TableBody $foot)
	{
		$this->foot = $foot;
	}
	
	public function setId($id)
	{
		$this->id = $id;
	}
	
	public function addClass($cls)
	{
		$this->classes[] = $cls;
	}
	
	private function getId()
	{
		return $this->id;
	}
	
	private function getClasses()
	{
		return implode(' ', $this->classes);
	}

	private function nl()
	{
		return "\n";
	}
	
	private function tab($c=0)
	{
		$t = "";
		for($x=0; $x<$c; $x++)
		{
			$t .= "\t";
		}
		return $t;
	}
	
	private function create()
	{	
		$this->strOut .= '<table'.$this->insertId().$this->insertClasses($this).'>'.$this->nl();
		
		$this->countMaxCells();
		
		$this->createHead();
		$this->createBody();
		$this->createFoot();
		
		$this->strOut .= '</table>'.$this->nl();
	}
	
	private function countMaxCells()
	{
		$this->maxCellsInRow($this->head);
		$this->maxCellsInRow($this->body);
		$this->maxCellsInRow($this->foot);
	}
	
	private function maxCellsInRow($obj)
	{
		if(empty($obj))
		{
			return;
		}
		foreach($obj->getRows() as $row)
		{
			$count = count($row->getCells());
			if($this->maxCells < $count)
			{
				$this->maxCells = $count;
			}
		}
	}
	
	private function insertId()
	{
		$id = $this->getId();
		if(!empty($id))
		{
			$id = ' id="'.$id.'"';
		}
		return $id;
	}
	
	private function insertClasses($obj)
	{
		$cls = $obj->getClasses();
		if(!empty($cls))
		{
			$cls = ' class="'.$cls.'"';
		}
		return $cls;
	}
	
	private function createHead()
	{
		if(empty($this->head))
		{
			return;
		}
		$this->dataField = 'th';
		$this->strOut .= $this->tab(1).'<thead'.$this->insertClasses($this->head).'>'.$this->nl();
		$this->createRows($this->head);
		$this->strOut .= $this->tab(1).'</thead>'.$this->nl();
	}
	
	private function createBody()
	{
		if(empty($this->body))
		{
			return;
		}
		$this->dataField = 'td';	
		$this->strOut .= $this->tab(1).'<tbody'.$this->insertClasses($this->body).'>'.$this->nl();
		$this->createRows($this->body);
		$this->strOut .= $this->tab(1).'</tbody>'.$this->nl();
	}
	
	private function createFoot()
	{
		if(empty($this->foot))
		{
			return;
		}
		$this->dataField = 'td';	
		$this->strOut .= $this->tab(1).'<tfoot'.$this->insertClasses($this->foot).'>'.$this->nl();
		$this->createRows($this->foot);
		$this->strOut .= $this->tab(1).'</tfoot>'.$this->nl();
	}
	
	private function createRows($obj)
	{
		$rows = $obj->getRows();
		foreach($rows as $row)
		{
			$this->strOut .=$this->tab(2).'<tr'.$this->insertClasses($row).'>'.$this->nl();
			$this->createCells($row, $obj);
			$this->strOut .= $this->tab(2).'</tr>'.$this->nl();
		}
	}
	
	private function createCells($row, $parentNode)
	{
		$cell = null;
		$cells = $row->getCells();		
		for($i=0; $i < $this->maxCells; $i++)
		{
			if(isset($cells[$i]))
			{
				$cell = $cells[$i];
			}
			else {
				$cell = array('data' => '', 'class' => '', 'colspan' => 0);
			}
			
			if($cell['colspan'] > 0)
			{
				$i = $i + $cell['colspan'] - 1;
			}
			
			$this->strOut .= $this->tab(3);
			$this->strOut .= '<'.$this->dataField.$this->insertAttr($cell, 'class').$this->insertAttr($cell, 'colspan').'>';
			$this->strOut .= $cell['data'];
			$this->strOut .= '</'.$this->dataField.'>';
			$this->strOut .= $this->nl();
		}
	}
	
	private function insertAttr($cell, $attr)
	{
		$value = $cell[$attr];
		if(empty($value))
		{
			return '';
		}
		return ' '.$attr.'="'.$value.'"';
	}
}