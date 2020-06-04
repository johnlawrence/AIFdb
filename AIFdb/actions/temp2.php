<?php

class Temp2Action extends Action {

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
            $ilist = "";

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
                            $ra .= $edgeFrom->nodeID . 'XXXX' . $edgeTo->nodeID . "\n";
                            $nc++;
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
                            $ca .= $edgeFrom->nodeID . 'XXXX' . $edgeTo->nodeID . "\n";
                            $nc++;
                        }
                    }
                }elseif($n->type == 'I'){
                    $ilist .= $n->nodeID . 'XXXX' . $n->text . "\n";
                }
            }
            
            $r = "***** Support\n" . $ra . "***** Conflict\n" . $ca . "INodes \n" . $ilist;

            common_template('clean', '', $r);
        }
    }


    function no_such_nodeset() {
        common_user_error('No such nodeset');
    }
}
