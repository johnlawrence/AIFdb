<?php

class NodeviewlinksAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($this->arg('id')){
            $show_types = array('I', 'RA', 'CA');
            $current_node = $this->arg('id');
            $node = Nodes::staticGet($current_node);
            $nodes_to = $node->getNodesIn();
            $nodes_from = $node->getNodesOut();

            $connected_nodes = array();
            $connected_nodes[] = $node->encodeJSON();
            $edges = array();

            $return = '{"to":[';
            while($nodes_to->fetch()){
                if(in_array($nodes_to->type, $show_types)){
                    $connected_nodes[] = $nodes_to->encodeJSON();
                    $edges[] = $this->edge_json($nodes_to, $node);
                    $outer_nodes = $nodes_to->getNodesIn();
                    while($outer_nodes->fetch()){
                        if(in_array($outer_nodes->type, $show_types)){
                            $connected_nodes[] = $outer_nodes->encodeJSON();
                            $edges[] = $this->edge_json($outer_nodes, $nodes_to);
                        }
                    }
                }
            }

            while($nodes_from->fetch()){
                if(in_array($nodes_from->type, $show_types)){
                    $connected_nodes[] = $nodes_from->encodeJSON();
                    $edges[] = $this->edge_json($node, $nodes_from);
                    $outer_nodes = $nodes_from->getNodesOut();
                    while($outer_nodes->fetch()){
                        if(in_array($outer_nodes->type, $show_types)){
                            $connected_nodes[] = $outer_nodes->encodeJSON();
                            $edges[] = $this->edge_json($nodes_from, $outer_nodes);
                        }
                    }
                }
            }

            $connected_nodes = array_unique($connected_nodes);
            $edges = array_unique($edges);

            $return = '{"nodes":[';
            $return = $return . implode(',', $connected_nodes);
            $return = $return . '],"edges":[';
            $return = $return . implode(',', $edges);
            $return = $return . ']}';

            common_template('clean', '', $return);
        }
    }

    function edge_json($from,$to) {
        if($from->type == 'CA' || $to->type == 'CA'){
            $agree = 'false';
        }else{
            $agree = 'true';
        }
        if($from->type != 'I'){ $l = 's'; }else{ $l = 'l'; }
        return '{"from":"'.$from->nodeID.'","to":"'.$to->nodeID.'","agree":'.$agree.',"length":"'.$l.'"}';
    }

    function no_such_node() {
        common_user_error('No such node.');
        exit;
    }
}
