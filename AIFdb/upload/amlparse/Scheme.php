<?php

Class Scheme
{
	private $name, $conclusion, $premiseArray, $criticalQuestionsArray;
	
	public function Scheme()
	{
		$this->premiseArray = array();
		$this->criticalQuestionsArray = array();
	}	
	
	public function setName($n)
	{
		if(empty($this->name))
			$this->name = $n;
	}
	
	public function setConclusion($c)
	{
		if(empty($this->conclusion))
			$this->conclusion = $c;		
	}
	
	public function addPremise($p)
	{
		if(strlen(trim($p)) > 0)
			array_push($this->premiseArray,$p);
	}
	
	public function addCriticalQuestion($c)
	{	
		if(strlen(trim($c)) > 0)
			array_push($this->criticalQuestionsArray,$c);			
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getConclusion()
	{
		return $this->conclusion;
	}
	
	public function getPremises()
	{
		return $this->premiseArray;
	}
	
	public function getCriticalQuestions()
	{
		return $this->criticalQuestionsArray;
	}
}

?>