<?php
class RTNLParser {
    var $nodes          = array();
    var $edges          = array();

    function RTNLParser ($source) {
        $start = strpos($source, 'End of Prelude');
        $start += 18;
        $source = substr($source, $start);

        $end = strpos($source, 'GetFernViews');
        $source = substr($source, 0, $end);

        $lines = preg_split("/(?:\r\n|\r|\n)/", $source);

        $get_text = false;
        foreach($lines as $line){
            if($get_text){
                preg_match('/^SetText\("([^"]*)"/', $line, $m);
                //echo "INode: ", $new_inode, ' ', $m[1]; 
                $this->nodes[] = array('id'=>$new_inode, 'type'=>'I', 'text'=>$m[1]);
                $get_text = false;
            }elseif(strpos($line,'Create(')){
                preg_match('/^([^ ]*) = Create\("([^"]*)"/', $line, $m);
                if($m[2] == 'Claim'){
                    $new_inode = $m[1];
                    $get_text = true;
                }
            }elseif(strpos($line,'CreateChild(')){
                preg_match('/^([^ ]*) = CreateChild\((.*), "([^"]*)"/', $line, $m);
                if($m[3] == 'Claim'){
                    $new_inode = $m[1];
                    $get_text = true;
                    //echo "Edge: ", $m[1], " to ", $m[2];
                    $this->edges[] = array('from'=>$m[1], 'to'=>$m[2]);
                }elseif($m[3] == 'Inference'){
                    $inference[$m[1]] = $m[2];
                }elseif($m[3] == 'CompoundReason'){
                    //echo "SNode: RA ", $m[1];
                    $this->nodes[] = array('id'=>$m[1], 'type'=>'RA', 'text'=>'RA');
                    $target = isset($inference[$m[2]]) ? $inference[$m[2]] : $m[2];
                    //echo "\nEdge: ", $m[1], " to ", $target;
                    $this->edges[] = array('from'=>$m[1], 'to'=>$target);
                }elseif($m[3] == 'CompoundObjection'){
                    //echo "SNode: CA ", $m[1];
                    $this->nodes[] = array('id'=>$m[1], 'type'=>'CA', 'text'=>'CA');
                    $target = isset($inference[$m[2]]) ? $inference[$m[2]] : $m[2];
                    //echo "\nEdge: ", $m[1], " to ", $target;
                    $this->edges[] = array('from'=>$m[1], 'to'=>$target);
                }
            }
            //echo " -  $line \n";
        }
    }
}
