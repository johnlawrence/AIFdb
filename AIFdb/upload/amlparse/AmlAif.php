<?php

include "AMLParser.php";

//sample usage
//$host = 'www.power-web.co.uk';
//$db = '/AIF2DB2/';
//$a = new AmlAif(file_get_contents("sample2.aml"),$host,$db,'test','pass');
//echo $a->addToDatabase();

/**
* A class for parsing AML, translating it into AIF2 and adding it to an AIFDB instance
* 
**/
class AmlAif{

	private $aml, $propsArray, $nodesToAdd, $parser, $nodeMappings, $dbParams, $logging, $schemes;

	/**
	* Constructor
	* @param $aml the actual aml (i.e. not just a file reference) to add to AIFDB
	**/
	public function AmlAif($aml, $db_host, $db_base_path, $db_user, $db_pass){
		$this->aml = $aml;
		$this->propsArray = array();
		$this->nodesToAdd = array();
		$this->parser = new AMLParser();
		
		$this->parser->getAML($aml);
		$this->parser->parse();

		$this->schemes = $this->parser->getSchemes();

		$this->nodeMappings = array();

		$this->logging = false;

		$this->dbParams = array("host" => $db_host, "base_path" => $db_base_path, "user" => $db_user, "pass" => $db_pass);
	}
	
	/**
	* Method to actually add the AML to the database
	**/
	public function addToDatabase(){
		$inodes = $this->parser->getPropArray();

		$nodeIds = array();

		$allParticipants = array();

		foreach($inodes as $inode){
			
			$addedNodeId = $this->addINode($inode);

			foreach($inode->getOwner() as $o){
				$allParticipants[$o][] = $addedNodeId;
			}				

			$this->nodeMappings[$inode->getId()] = $addedNodeId;
			$nodeIds[] = $this->nodeMappings[$inode->getId()]->nodeID;

			$refutation = $inode->getRefutation();
			$snode = $this->createSnode($refutation);			
		}

		$keys = array_keys($allParticipants);

		foreach($keys as $key){
			$firstName = "";
			$surname = "";

			$theName = explode(" ",$key);
			$size = sizeof($theName);

			/*if($size > 1){
				$surname = $theName[$size - 1];
				for($i=0;$i<$size-1;$i++){
					$firstName .= $theName[$i] . " ";
				}
			}else{
				$surname = $key;
				$firstName = $key;
			}*/
			$firstName = $theName[0];
			$surname = $theName[1];


			$lnodes = array();

			/* Create L and Y nodes for each locution and attach to the relevant INode */
			foreach($allParticipants[$key] as $inode){
				$response = $this->db_post("nodes/", '{"type":"L","text":"' . $key . ' says \'' . $inode->text . '\'"}');
				$splitResponseL = explode("text/html",$response);
				$lnode = json_decode(trim($splitResponseL[1]));

				$nodeIds[] = $lnode->nodeID;
				$lnodes[] = $lnode->nodeID;

				$response = $this->db_post("nodes/", '{"type":"Y","text":"YA"}');
				$splitResponseY = explode("text/html",$response);
				$ynode = json_decode(trim($splitResponseY[1]));

				$nodeIds[] = $ynode->nodeID;

				$response = $this->db_post("edges/", '{"fromID":' . $lnode->nodeID . ',"toID":' . $ynode->nodeID . '}');
				$response = $this->db_post("edges/", '{"fromID":' . $ynode->nodeID . ',"toID":' . $inode->nodeID . '}');			
			}

			$participant = '{"firstName":"' . $firstName . '","surname":"' . $surname . '"}';
			$response = $this->db_post("/people",$participant);	
			$splitResponse = explode("text/html",$response);
			$person = json_decode(trim($splitResponse[1]));
	
			foreach($lnodes as $lnode)
				$response = $this->db_post("/locutions",'{"personID":"' . $person->personID . '","nodeID":"' . $lnode . '"}');
		}


		foreach($this->parser->getLinkedArray() as $arg){
			$to = "";
			foreach($arg as $node){
				$to = $node->getEdgeTo();				
				$addedNode = $this->addINode($node);

				$nodeIds[] = $addedNode->nodeID;
				$this->nodeMappings[$node->getId()] = $this->addINode($node);
			}			
		}

		$allParticipants = array();

		foreach($inodes as $inode){
			if($inode->getEdgeTo()==null)
				continue;
	
			$from = $this->nodeMappings[$inode->getId()];
			$to = $this->nodeMappings[$inode->getEdgeTo()->getId()];

			$theSchemeId = array_intersect($inode->getScheme(),$inode->getEdgeTo()->getScheme());
			$theScheme = "";

			if(sizeof($theSchemeId)!=0)
				$theScheme = trim($this->schemes[$theSchemeId[0]]);
			// "Scheme name = " . $theScheme;

			$scheme = $this->searchForScheme($theScheme);			
			//echo "Scheme: " . $scheme;

			$ref = $inode->getRefutation();
				
			$addedSnode = $this->addSNode($ref);

			if($scheme!=-1){
				$this->db_post("schemefulfillment",'{"nodeID":' . $addedSnode->nodeID . ',"schemeID":' . $scheme . '}');
			}
			
			$nodeIds[] = $addedSnode->nodeID;

			$edge1 = $this->addEdge($from->nodeID,$addedSnode->nodeID);	
			$edge2 = $this->addEdge($addedSnode->nodeID,$to->nodeID);	

			/* If we've created a CA node, make it bi-directional to remain
				faithful to the original Araucaria notation... */
			//if($ref){
				//$addedSnode = $this->addSNode($ref);
				//$edge1 = $this->addEdge($to,$addedSnode->nodeID);	
				//$edge2 = $this->addEdge($addedSnode->nodeID,$from);
			//}		
		}

		foreach($this->parser->getLinkedArray() as $arg){
			$to = "";
			$ref = false;
			$actualTo = null;

			foreach($arg as $node){		
				$actualTo = $node->getEdgeTo();		
				$to = $this->nodeMappings[$node->getEdgeTo()->getId()];
				$ref = $node->getRefutation();
				break;
			}
			$tempNode = $this->nodeMappings[$arg[0]->getId()];
			//print_r($arg[0]);
;

			$theSchemeId = array_intersect($arg[0]->getScheme(),$actualTo->getScheme());
			$theScheme = "";

			if(sizeof($theSchemeId)!=0)
				$theScheme = trim($this->schemes[$theSchemeId[0]]);	

			$scheme = $this->searchForScheme($theScheme);	

			//echo "Scheme: " . $scheme;

//			if($scheme!=-1){
//				$response = $this->db_post("schemefulfillment",'{"nodeID":' . $addedSnode->nodeID . ',"schemeID":' . $scheme . '}');
//				print_r($response);
//			}	

			$addedSnode = $this->addSnode($ref);

			if($scheme!=-1){
                                $response = $this->db_post("schemefulfillment",'{"nodeID":' . $addedSnode->nodeID . ',"schemeID":' . $scheme . '}');
                                //print_r($response);
                        }


			$nodeIds[] = $addedSnode->nodeID;
			
			foreach($arg as $node){
				$from = $this->nodeMappings[$node->getId()];
				$edge1 = $this->addEdge($from->nodeID,$addedSnode->nodeID);	
			}	

			$edge2 = $this->addEdge($addedSnode->nodeID,$to->nodeID);				
		}		

		$response = $this->db_post('nodesets/new/', "");
		$splitResponse = explode("text/html",$response);
		$nodeset = json_decode(trim($splitResponse[1]));

		$nsID = $nodeset->nodeSetID;

		foreach($nodeIds as $id){
			$this->db_post('nodesetmappings', '{"nodeSetID":' . $nodeset->nodeSetID . ',"nodeID":' . $id . '}');
		}

		return $nsID;
	}

	public function searchForScheme($scheme){

		$ignoreWords = array("a", "an");
		$schemeWords = explode(" ",$scheme);

		$bunchedScheme = "";
		$startAt = (strtolower($schemeWords[0].$schemeWords[1])=="argumentfrom") ? 2 : 0;

		for($i=$startAt;$i<sizeof($schemeWords);$i++){
			if(!in_array($schemeWords[$i],$ignoreWords)){
				$bunchedScheme .= $this->getCleanName($schemeWords[$i]);
			}
		}

		$response = $this->db_post("schemes/search",'{"name":"' . $scheme . '"}');
		$splitResponse = explode("text/html",$response);
		if($splitResponse[1]==""){
			return -1;
		}else{
			$theScheme = json_decode(trim($splitResponse[1]));	
			return $theScheme->schemeID;
		}		
	}

	private function getCleanName($word){
		$first = substr($word,0,1);
		$rest = substr($word, 1,strlen($word)-1);
		return strToUpper($first) . strToLower($rest);
	}

	private function db_post($path, $data)
        {
                $reply = "";
		
		$host = $this->dbParams["host"];
		$user = $this->dbParams["user"];
		$pass = $this->dbParams["pass"];

		$path = $this->dbParams["base_path"] . $path;

                $fp = fsockopen($host, 80, $err_num, $err_msg, 10);
                if (!$fp) {
                        $reply = "$err_msg ($err_num)";
                } else {
                        $auth = base64_encode($user.":".$pass);

                        fputs($fp, "POST $path HTTP/1.1\r\n");
                        fputs($fp, "Authorization: Basic ".$auth."\r\n");
                        fputs($fp, "Host: $host\r\n");
                        fputs($fp, "Content-type: text/html\r\n");
                        fputs($fp, "Content-length: ".strlen($data)."\r\n");
                        fputs($fp, "Connection: close\r\n\r\n");
                        fputs($fp, $data);

                        while ($line = fgets($fp)) $reply .= $line;
                        
                        

                        fclose($fp);
                }
                return $reply;
        }

	private function createSnode($ref){
		if($ref){
			return '{"type":"CA","text":"CA"}';
		}else{
			return '{"type":"RA","text":"RA"}';
		}			
	}

	private function addINode($node){
	$nodeToAdd = '{"text":"' . $node->getText() . '","type":"I","owner":"' . $node->getOwner() . '"}';
	$response = $this->db_post('nodes/', $nodeToAdd);
	$splitResponse = explode("text/html",$response);
	$splitResponse[1] . "\n\n";
	return json_decode(trim($splitResponse[1]));
	}

	private function addSNode($ref=false){
		$type = ($ref) ? "CA" : "RA";
		$theNode = '{"type":"' . $type . '","text":"' . $type . '"}';
		$response = $this->db_post('nodes/', $theNode);
		return $this->parseResponse($response);
	}

	private function addEdge($from,$to){
		$theEdge = '{"fromID":' . $from . ',"toID":' . $to . '}';
		$response = $this->db_post('edges/', $theEdge);
		return $this->parseResponse($response);
	}

	/**
	* Method to parse a response, stripping off the HTTP headers and returning a parse JSON object
	* @param $response the response to parse
	* @return PHP object representing the JSON object
	**/
	private function parseResponse($response){
		$splitResponse = explode("text/html",$response);
		$splitResponse[1] . "\n\n";
		return json_decode(trim($splitResponse[1]));
	}

	private function createEdge($from,$to){
		return '{"fromID":' . $from . ',"toID":' . $to . '}';
	}

}
?>
