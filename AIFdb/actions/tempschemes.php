<?php

class TempschemesAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($this->arg('multi')){
            $post_data = $this->arg('multi'); // USE GET!!
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
                        if($edgeTo->type != "I" && $edgeTo->type != "L"){ break; }
                        $ra .= $n->nodeID . "\n";
                        $ra .= "C: " . $edgeTo->text . "\n";
                        while($edgeFrom->fetch()){
                            if($edgeFrom->type != "I" && $edgeFrom->type != "L"){ break; }
                            $ra .= "P(" . $edgeFrom->type . "): " . $edgeFrom->text . "\n";
                            $nc++;
                            $Is[] = $edgeTo->nodeID . " - " . $edgeTo->text;
                            $Is[] = $edgeFrom->nodeID . " - " . $edgeFrom->text;
                        }
                    }
                }
            }
            
            $io = implode("\n", array_unique($Is));
            $r = $nc . "\n\n" . $ra . "\n\n" .$io;

            common_template('clean', '', $r);
        }
    }


    function no_such_nodeset() {
        common_user_error('No such nodeset');
    }
}
