<?php

class TempAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($this->arg('multi')){
            $post_data = file_get_contents('php://input');
            $insets = Nodes::getInSet($post_data);
            $ra = "";
            $ca = "";
            $u = "";
            $Is = array();
            $link = array();
            $Ii = 0;
            $nc = 0;
            $nu = 0;

            while($insets->fetch()){
                $n = clone $insets;
                $nodekeys[$n->nodeID] = $n;
                if($n->type == 'RA'){
                    $edgeTo = $n->getNodesOut();
                    $edgeFrom = $n->getNodesIn();
                    $edge_out = "";
                    while($edgeTo->fetch()){
                        if($edgeTo->type != "I"){ break; }
                        while($edgeFrom->fetch()){
                            if($edgeFrom->type != "I"){ break; }
                            $ra .= $edgeFrom->text . 'XXXX' . $edgeTo->text . "\n";
                            $nc++;
                            $link[$edgeFrom->nodeID . "-" . $edgeTo->nodeID] = 1;
                            $link[$edgeTo->nodeID . "-" . $edgeFrom->nodeID] = 1;
                        }
                    }
                }elseif($n->type == 'CA'){
                    $edgeTo = $n->getNodesOut();
                    $edgeFrom = $n->getNodesIn();
                    $edge_out = "";
                    while($edgeTo->fetch()){
                        if($edgeTo->type != "I"){ break; }
                        while($edgeFrom->fetch()){
                            if($edgeFrom->type != "I"){ break; }
                            $ca .= $edgeFrom->text . 'XXXX' . $edgeTo->text . "\n";
                            $nc++;
                            $link[$edgeFrom->nodeID . "-" . $edgeTo->nodeID] = 1;
                            $link[$edgeTo->nodeID . "-" . $edgeFrom->nodeID] = 1;
                        }
                    }
                }elseif($n->type == 'I'){
                    $Is[$Ii] = $n;
                    $Ii++;
                }
            }

            shuffle($Is);

            foreach ($Is as $key => $value) {
                for($n = $key+1; $n<$Ii; $n++){
                    if (array_key_exists($value->nodeID . '-' . $Is[$n]->nodeID, $link)) {
                    }else{
                        $u .= $value->text . 'XXXX' . $Is[$n]->text . "\n";
                        $nu++;
                        //if($nu >= $nc){ break 2;}
                        break;
                    }
                    //if($nu >= $nc){ break 2;}
                }
            }

            $r = "***** Support\n" . $ra . "***** Conflict\n" . $ca . "***** Un\n" . $u;
            //$r .= $nu . " " . $nc;

            common_template('clean', '', $r);
        }
    }


    function no_such_nodeset() {
        common_user_error('No such nodeset');
    }
}
