<?php
class PLParser {
    var $nodes          = array();
    var $edges          = array();

    function PLParser ($source) {
        $lines = preg_split("/(?:\r\n|\r|\n)/", $source);
        foreach($lines as $line){
            if(strpos($line,'aif_node') !== false){
                preg_match('/^aif_node\(([0-9]*), (.*), aif_([A-Z]*), date\(.*\)\)/', $line, $m);
                $nodeID = $m[1];
                $nodeType = $m[3];

                if($nodeType == 'I' || $nodeType == 'L'){
                    $nodeText = $this->dirtytext($m[2]);
                }else{
                    $nodeText = str_replace("aif_", "", $m[2]);
                    $nodeText = str_replace("_", " ", $nodeText);
                }
                $this->nodes[] = array('id'=>$nodeID, 'type'=>$nodeType, 'text'=>$nodeText);
                //echo "ID: " . $nodeID . "\n";
                //echo "Type: " . $nodeType . "\n";
                //echo "Text: " . $nodeText . "\n";
                //echo "---- \n";
            }elseif(strpos($line,'aif_edge') !== false){
                preg_match('/^aif_edge\(([0-9]*), ([0-9]*)\)/', $line, $m);
                //echo "Edge: ", $m[1], " to ", $m[2];
                $this->edges[] = array('from'=>$m[1], 'to'=>$m[2]);
            }
        }
    }

    function dirtytext($t) {
        $t = str_replace(",',',", "PLCOMMA ", $t);
        $t = str_replace("'", "", $t);
        $t = str_replace(",simplequote,", "'", $t);
        $t = str_replace(",", " ", $t);
        $t = str_replace("PLCOMMA", ",", $t);
        $t = preg_replace('/^\[/', '', $t);
        $t = preg_replace('/\]$/', '', $t);

        return $t;
    }
}
