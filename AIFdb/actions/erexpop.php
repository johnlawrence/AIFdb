<?php

class ErexpopAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($this->arg('multi')){
            $post_data = file_get_contents('php://input');
            $insets = Nodes::getInSet($post_data);
            $r = "";
            $p = "";
            $c = "";

            while($insets->fetch()){
                $n = clone $insets;
                $nodekeys[$n->nodeID] = $n;
                if($n->type == 'RA' && $n->text == 'ERExpert Opinion'){
                    //$r .= $n->text . ":";
                    $nin = $n->getNodesIn();
                    $nout = $n->getNodesOut();
                    while($nin->fetch()){
                        if($nin->type == 'I'){
                            $p .= $nin->text . "\n";
                        }
                    }
                    while($nout->fetch()){
                        if($nout->type == 'I'){
                            $c .= $nout->text . "\n";
                        }
                    }
                }
            }

            $r .= "PREM:\n" . $p . "\n\nCONC:\n" . $c;

            common_template('clean', '', $r);
        }
    }


    function no_such_nodeset() {
        common_user_error('No such nodeset');
    }
}
