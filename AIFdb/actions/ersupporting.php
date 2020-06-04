<?php

class ErsupportingAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($this->arg('multi')){
            $post_data = file_get_contents('php://input');
            $insets = Nodes::getInSet($post_data);

            while($insets->fetch()){
                $n = clone $insets;
                $nodekeys[$n->nodeID] = $n;
                if($n->type == 'I'){
                    $nin = $n->getNodesOut();
                    while($nin->fetch()){
                        if($nin->type == 'RA'){
                            $r .= $n->text . "\n";
                            break;
                        }
                    }
                }
            }

            common_template('clean', '', $r);
        }
    }


    function no_such_nodeset() {
        common_user_error('No such nodeset');
    }
}
