<?php
class LKIFParser {
    var $nodes          = array();
    var $edges          = array();

    function LKIFParser ($source) {
        $obj = new SimpleXMLElement($source);
        $nodet = array();
        foreach ($obj->{'argument-graphs'}->{'argument-graph'}[0]->statements->statement as $statement){
            $this->nodes[] = array('id'=>$statement->attributes()->id, 'type'=>'I', 'text'=>trim($statement->s));
            $node_inverse['NI'.$statement->attributes()->id] = 'It is not the case that ' . trim($statement->s);
        }

        $i=1; $j=1;
        foreach ($obj->{'argument-graphs'}->{'argument-graph'}[0]->arguments->argument as $argument){
            if($argument->attributes()->direction == "con"){
                $this->nodes[] = array('id'=>"AIFS".$i, 'type'=>'CA', 'text'=>'CA');
                
                $this->edges[] = array('from'=>"AIFS".$i, 'to'=>$argument->conclusion->attributes()->statement);

                foreach($argument->premises->premise as $premise){
                    $this->edges[] = array('from'=>$premise->attributes()->statement, 'to'=>"AIFS".$i);
                }
            }else{
                $this->nodes[] = array('id'=>"AIFS".$i, 'type'=>'RA', 'text'=>'RA');

                $this->edges[] = array('from'=>"AIFS".$i, 'to'=>$argument->conclusion->attributes()->statement);

                foreach($argument->premises->premise as $premise){
                    if($premise->attributes()->polarity == "negative"){
                        $inv_key = 'NI'.$premise->attributes()->statement;
                        if(isset($added[$inv_key])){
                            $inv_id = $added[$inv_key];
                        }else{
                            $negtext = $node_inverse[$inv_key];
                            $this->nodes[] = array('id'=>"AIFNI".$j, 'type'=>'I', 'text'=>$negtext);
                            $added[$inv_key] = "AIFNI".$j;
                            $inv_id = "AIFNI".$j;
                            $j++;

                            // add CA N->NEG
                            $this->nodes[] = array('id'=>"AIFSX".$j, 'type'=>'CA', 'text'=>'CA');
                            // edge N->CA
                            $this->edges[] = array('from'=>$premise->attributes()->statement, 'to'=>"AIFSX".$j);
                            //edge CA->NEG
                            $this->edges[] = array('from'=>"AIFSX".$j, 'to'=>$inv_id);
                            $j++;

                            // add CA NEG->N
                            $this->nodes[] = array('id'=>"AIFSX".$j, 'type'=>'CA', 'text'=>'CA');
                            // edge NEG->CA
                            $this->edges[] = array('from'=>$inv_id, 'to'=>"AIFSX".$j);
                            //edge CA->N
                            $this->edges[] = array('from'=>"AIFSX".$j, 'to'=>$premise->attributes()->statement);
                            $j++;
                        }
                        $premise_id = $inv_id;
                    }else{
                        $premise_id = $premise->attributes()->statement;
                    }

                    if($premise->attributes()->type != "exception"){
                        $this->edges[] = array('from'=>$premise_id, 'to'=>"AIFS".$i);
                    }else{
                        // add new CA
                        $this->nodes[] = array('id'=>"AIFSX".$j, 'type'=>'CA', 'text'=>'CA');
                        
                        // edge CA->RA
                        $this->edges[] = array('from'=>"AIFSX".$j, 'to'=>"AIFS".$i);

                        //edge I->CA
                        $this->edges[] = array('from'=>$premise_id, 'to'=>"AIFSX".$j);

                        $j++;
                    }
                }
            }
            $i++;
        } 
    }
}
