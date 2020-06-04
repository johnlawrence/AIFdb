<?php
class LKIFParser {
    var $nodes          = array();
    var $edges          = array();
    
    var $nodet          = '';
    
    function LKIFParser ($source) {
        $obj = new SimpleXMLElement($source);
        foreach ($obj->{'argument-graphs'}->{'argument-graph'}[0]->statements->statement as $statement){
            $this->nodes[] = array('id'=>$statement->attributes()->id, 'type'=>'I', 'text'=>trim($statement->s));
            //$this->nodet .= "\nINode:: ";
            //$this->nodet .= "ID:" . $statement->attributes()->id;
            //$this->nodet .= ", Text:" . trim($statement->s);
        }

        $i=1; $j=1;
        foreach ($obj->{'argument-graphs'}->{'argument-graph'}[0]->arguments->argument as $argument){
            if($argument->attributes()->direction == "con"){
                $this->nodes[] = array('id'=>"AIFS".$i, 'type'=>'CA', 'text'=>'CA');
                //$this->nodet .= "\nSNode:: ";
                //$this->nodet .= "Type:CA";
                //$this->nodet .= ", ID=AIFS".$i;
                
                $this->edges[] = array('from'=>"AIFS".$i, 'to'=>$argument->conclusion->attributes()->statement);
                //$this->nodet .= "\nEdge::  ";
                //$this->nodet .= "From:AIFS".$i;
                //$this->nodet .= ", To:".$argument->conclusion->attributes()->statement;

                foreach($argument->premises->premise as $premise){
                    $this->edges[] = array('from'=>$premise->attributes()->statement, 'to'=>"AIFS".$i);
                    //$this->nodet .= "\nEdge::  ";
                    //$this->nodet .= "From:".$premise->attributes()->statement;
                    //$this->nodet .= ", To:AIFS".$i;
                }
                $i++;
            }else{
                $this->nodes[] = array('id'=>"AIFS".$i, 'type'=>'RA', 'text'=>'RA');
                //$this->nodet .= "\nSNode:: ";
                //$this->nodet .= "Type:RA";
                //$this->nodet .= ", ID=AIFS".$i;

                $this->edges[] = array('from'=>"AIFS".$i, 'to'=>$argument->conclusion->attributes()->statement);
                //$this->nodet .= "\nEdge::  ";
                //$this->nodet .= "From:AIFS".$i;
                //$this->nodet .= ", To:".$argument->conclusion->attributes()->statement;

                foreach($argument->premises->premise as $premise){
                    if($premise->attributes()->exception == "false"){
                        $this->edges[] = array('from'=>$premise->attributes()->statement, 'to'=>"AIFS".$i);
                        //$this->nodet .= "\nEdge::  ";
                        //$this->nodet .= "From:".$premise->attributes()->statement;
                        //$this->nodet .= ", To:AIFS".$i;
                    }else{
                        // add new CA
                        $this->nodes[] = array('id'=>"AIFSX".$j, 'type'=>'CA', 'text'=>'CA');
                        //$this->nodet .= "\nSNode:: ";
                        //$this->nodet .= "Type:CA";
                        //$this->nodet .= ", ID=AIFSX".$j;
                        
                        // edge CA->RA
                        $this->edges[] = array('from'=>"AIFSX".$j, 'to'=>"AIFS".$i);
                        //$this->nodet .= "\nEdge::  ";
                        //$this->nodet .= "From:AIFSX".$j;
                        //$this->nodet .= ", To:AIFS".$i;

                        //edge I->CA
                        $this->edges[] = array('from'=>$premise->attributes()->statement, 'to'=>"AIFSX".$j);
                        //$this->nodet .= "\nEdge::  ";
                        //$this->nodet .= "From:".$premise->attributes()->statement;
                        //$this->nodet .= ", To:AIFSX".$j;

                        $j++;
                    }
                }
                $i++;
            }
        } 

        $this->nodet .= "\n\n";
    }
    
}
