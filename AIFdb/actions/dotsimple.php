<?php

class DotsimpleAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($this->arg('nodesetid')){
            $nodeset_id = $this->arg('nodesetid');
            $nodeset = NodeSets::staticGet('nodeSetID', $nodeset_id);

            if (!$nodeset) {
                $this->no_such_nodeset();
                return;
            }

            $output = "digraph nodeset" . $nodeset_id . " {";
            $output .= $this->generate_dot($nodeset);
            $output .= "}";

            common_template('dotsimple', '', $output, array('nodeset' => $nodeset_id));
        }
    }

    function no_such_nodeset() {
        common_user_error('No such nodeset');
    }

    function generate_dot($nodeset) {
        $node = $nodeset->getNodes2();
        $node->find();

        $nodes_out = "";

        while($node->fetch()){
            if($node->type == 'I'){
                $nodes_out .= $this->show_node($node);
            }elseif($node->type == 'RA' || $node->type == 'CA'){
                $edges_out .= $this->show_edge($node);
            }
        }

        $dot_out = $nodes_out . $edges_out;
        return $dot_out;
    }

    function show_node($node) {
        $node_out = ' ';
        $node_out .= $node->nodeID;
        $node_out .= ' [label="';
        $node_out .= $node->text;
        $node_out .= '"];';
        $node_out .= "";
        return $node_out;
    }

    function show_edge($node) {
        $edgeTo = $node->getNodesOut();
        $edgeFrom = $node->getNodesIn();
        $edge_out = "";
        while($edgeTo->fetch()){
            if($edgeTo->type != "I"){ break; }
            while($edgeFrom->fetch()){
                if($edgeFrom->type != "I"){ break; }
                $edge_out .= ' ';
                $edge_out .= $edgeFrom->nodeID;
                $edge_out .= ' -> ';
                $edge_out .= $edgeTo->nodeID;
                if($node->type == 'RA'){
                    $edge_out .= '[color="#00ff00"]';
                }else{
                    $edge_out .= '[color="#ff0000"]';
                }
                $edge_out .= ";";
            }
        }
        return $edge_out;
    }
}
