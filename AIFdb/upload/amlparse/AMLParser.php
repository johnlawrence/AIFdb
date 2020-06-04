<?php

error_reporting(E_ALL ^ E_NOTICE);

include "Prop.php";
include "Scheme.php";

//$p = new AMLParser();
//$p->getAml(file_get_contents("sample4.aml"));
//$p->parse();


class AMLParser
{
	private $aml, $text, $currentElement, $currentProp, $currentScheme, $nodeStack = array(), $handlersArray = array(), $dataHanldersArray = array(), $endHandlersArray = array(), $propsArray = array(), $schemesArray = array(), $linkedArray = array(), $hitCALA = false, $openAU = 0, $laCount = 0, $inLA = false, $inCA = false, $author, $date, $source, $refutation = false, $schemes = array(), $laNest, $currentLaID, $currentNestAppend;
	
	public function AMLParser()
	{
		$this->handlersArray = array("SCHEME" => '$this->currentScheme = new Scheme();',
										"PROP" => '$this->handleProp($attribs);',
										"INSCHEME" => '$this->handleScheme($attribs);',
										"AU" => '$this->openAU++;',
										"LA" => '$this->handleLA();',
										"CA" => 'array_push($this->nodeStack,$this->currentProp); $this->inCA = true;',
										"AUTHOR" => '$this->currentElement = "AUTHOR";',
										"DATE" => '$this->currentElement = "DATE";',
										"OWNER" => '$this->currentProp->setOwner($attribs["NAME"]);',
										"SOURCE" => '$this->currentElement = "SOURCE";',
										"REFUTATION" => '$this->refutation = true;');
										
		$this->dataHandlersArray = array("NAME" => '$this->currentScheme->setName($data);',
											"PREMISE" => '$this->currentScheme->addPremise($data);',
											"CONCLUSION" => '$this->currentScheme->setConclusion($data);',
											"CQ" => '$this->currentScheme->addCriticalQuestion($data);',
											"TEXT" => '$this->text .= $data;',
											"PROPTEXT" => '$this->currentProp->setText($data);',
											"AUTHOR" => '$this->author .= $data;',
											"DATE" => '$this->date .= $data;',							
											"SOURCE" => '$this->source .= $data;');
										
		$this->endHandlersArray = array("SCHEME" => 'array_push($this->schemesArray,$this->currentScheme);',
										"PROP" => '$this->handleEndProp();',
										"CA" => '$this->hitCALA = true; $this->inCA = false;',
										"LA" => '$this->hitCALA = true; $this->handleEndLA();',
										"AU" => '$this->handleEndAU();',
										"PROP" => '$this->handleEndProp();',
										"REFUTATION" => '$this->refutation = false;');
										
		$this->laCount = 0;
		$this->laNest = 0;										
		$this->currentLaID = 0;

		$currentNestAppend = array();
	}	

	public function handleScheme($attribs){
		if(!isset($this->schemes[$attribs["SCHID"]])){
			$this->schemes[$attribs["SCHID"]] = trim($attribs["SCHEME"]);
		}

		$this->currentProp->setInScheme($attribs["SCHID"]);		
	}

	public function getSchemeById($id){
		return $this->schemes[$id];
	}

	public function getAML($a)
	{
		$this->aml = $a;
		//$this->aml = file_get_contents("sample1.aml");
		$this->aml = str_replace("","",$this->aml);
	}
	
	public function getAuthor()
	{
		return $this->author;
	}
	
	public function getDate()
	{
		return $this->date;
	}
	
	public function getSource()
	{
		return $this->source;
	}
	
	public function parse()
	{
		$this->parser = xml_parser_create();
		xml_set_object($this->parser,$this);
		xml_set_element_handler($this->parser,"startElement","endElement");
		xml_set_character_data_handler($this->parser,"getData");
		
		xml_parse($this->parser,$this->aml);		
		
		//print_r($this->linkedArray);
		//print_r($this->propsArray);
	}
	
	private function startElement($p, $tagName, $attribs)
	{	
		$this->hitCALA = false;		
		
		if(array_key_exists($tagName,$this->handlersArray)){
			eval($this->handlersArray[$tagName]);
		}
		
		$this->currentElement = $tagName;		
	}
	
	private function handleProp($a)
	{	
		$this->currentProp = new Prop($a["IDENTIFIER"],$a["MISSING"],$this->refutation);
		$this->refutation = false;
		
		if(!empty($this->nodeStack))
			$this->currentProp->setEdgeTo($this->nodeStack[$this->openAU - 1]);
		
		$this->nodeStack[$this->openAU] = $this->currentProp;
	}

	private function handleLA()
	{	
		$this->inCA = false;
		array_push($this->nodeStack,$this->currentProp);

		if($this->laNest==0)
			$this->laCount++;
		$this->laNest++;

		$this->currentLaID = floor($this->laCount . $this->laNest);

		if(!isset($this->currentNestAppend[$this->currentLaID]))
			$this->currentNestAppend[$this->currentLaID] = 0;
		else
			$this->currentNestAppend[$this->currentLaID]++;
		
		//$this->linkedArray[$this->laCount] = array($this->currentProp);
	}
	
	private function handleEndProp()
	{
		if($this->laNest > 0 && !$this->inCA && !$this->currentProp->getRefutation())
		{
			if(!is_array($this->linkedArray[floor($this->currentLaID . $this->currentNestAppend[$this->currentLaID])]))
				$this->linkedArray[floor($this->currentLaID . $this->currentNestAppend[$this->currentLaID])] = array($this->currentProp);
			else
				array_push($this->linkedArray[floor($this->currentLaID . $this->currentNestAppend[$this->currentLaID])],$this->currentProp);
		}else
		{
			array_push($this->propsArray,$this->currentProp);
		}
	}
	
	private function handleEndAU()
	{
		$this->nodeStack[$this->openAU] = null;
		$this->openAU--;
	}
	
	private function handleEndLA()
	{		
		$this->laNest--;
		$this->currentLaID = floor($this->laCount . $this->laNest);		
	}

	private function endElement($p, $tagName)
	{	
		if(array_key_exists($tagName,$this->endHandlersArray))
			eval($this->endHandlersArray[$tagName]);		
	}
	
	private function getData($p, $data)
	{			
		if(trim($data)==""){
			return;
		}

		if(array_key_exists($this->currentElement,$this->dataHandlersArray))
			eval($this->dataHandlersArray[$this->currentElement]);
	}
	
	public function getPropArray()
	{
		return $this->propsArray;
	}
	
	public function getText()
	{
		return str_replace('"','\"',$this->text);	
	}
	
	public function getSchemesArray()
	{
		return $this->schemesArray;
	}
	
	public function getLinkedArray()
	{
		return $this->linkedArray;
	}

	public function getSchemes(){
		return $this->schemes;
	}
}

?>
