<?php

Class Prop
{
	private $text, $id, $edgeTo, $isRefutation, $missing, $inScheme = array();
	
	public function Prop($i,$m, $r)
	{
		$this->id = $i;
		$this->missing = ($m=="yes") ? "true" : "false";
		$this->isRefutation = $r;
		$this->owner = array();
		$this->edgeTo = null;
	}	
	
	public function setText($t)
	{
		$this->text .= $t;
	}
	
	public function setEdgeTo($e)
	{
		$this->edgeTo = $e;
	}
	
	public function setInScheme($s)
	{
		if(strlen($s) > 0)
			array_push($this->inScheme,$s);
	}
	
	public function setRefutation($r)
	{
		$this->isRefutation = $r;
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function getText()
	{
		return str_replace('"',' ',$this->text) . "."; //preg_replace('/[:alnum:][:^alnum:]*$/', '', $this->text);
	}
	
	public function getEdgeTo()
	{
		return $this->edgeTo;
	}
	
	public function getRefutation()
	{
		return $this->isRefutation;
	}
	
	public function getMissing()
	{
		return $this->missing;
	}
	
	public function getScheme()
	{
		return $this->inScheme;
	}

	public function setOwner($o)
	{	
		$this->owner[] = $o;
	}

	public function getOwner()
	{
		return $this->owner;
	}
}

?>
