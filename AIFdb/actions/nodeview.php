<?php

class NodeviewAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($this->arg('node')){
            $node_id = $this->arg('node');
            $node = Nodes::staticGet('nodeID', $node_id);

            if (!$node) {
                $this->no_such_node();
                return;
            }

            $output = "";
            common_template('nodeview', '', $output, array('node' => $node_id));
        }
    }

    function no_such_node() {
        common_user_error('No such node');
    }

}
