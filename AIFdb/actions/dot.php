<?php

class DotAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($this->arg('multi')){
            $post_data = file_get_contents('php://input');
            $DT_nodes = array();
            $DT_edges = array();
            $insets = Nodes::getInSet($post_data);

            while($insets->fetch()){
                $DT_nodes[] = $this->show_node($insets);
            }

            $einsets = Edges::getInSet($post_data);
            while($einsets->fetch()){
                $edge_out = " ";
                $edge_out.= $einsets->fromID;
                $edge_out.= "->";
                $edge_out.= $einsets->toID;
                $edge_out.= ";";
                $DT_edges[] = $edge_out;
            }

            $node_output = implode("\n", $DT_nodes);
            $edge_output = implode("\n", $DT_edges);

            $output = "digraph all {\n";
            $output.= $node_output . "\n" . $edge_output;
            $output.= "}";
            common_template('clean', '', $output);
        }elseif($this->arg('nodesetid')){
            $nodeset_id = $this->arg('nodesetid');
            if($nodeset_id == 'all'){
                $nodes = new Nodes;
                $nodes->find();
                $nodes_out = "";
                while($nodes->fetch()){
                    if($nodes->type == 'I'){
                        $nodes_out .= $this->show_node($nodes);
                    }else{
                        $edges_out .= $this->show_edge($nodes);
                    }
                }

                $output = "digraph all {\n";
                $output .= $nodes_out . $edges_out;
                $output .= "}";

            }else{
                $nodeset = NodeSets::staticGet('nodeSetID', $nodeset_id);

                if (!$nodeset) {
                    $this->no_such_nodeset();
                    return;
                }

                $output = "digraph nodeset" . $nodeset_id . " {\n";
                if($this->arg('layout') && $this->arg('plus')){
                    $output .= 'rankdir=RL;'."\n";
                }
                $output .= $this->generate_dot($nodeset);
                $output .= "}";
            }

            if($this->arg('layout')){
                common_template('dotsimple', '', $output, array('nodeset' => $nodeset_id));
            }else{
                common_template('clean', '', $output);
            }
        }
    }

    function no_such_nodeset() {
        common_user_error('No such nodeset');
    }

    function generate_dot($nodeset) {
        $plus = false;
        if($this->arg('plus')){
            $plus = true;
        }

        $node = $nodeset->getNodes2();
        $node->find();
        
        $nodes_out = "";

        $edges = array();

        while($node->fetch()){
            if(!$plus && $node->type != 'I' && $node->type != 'RA' && $node->type != 'CA'){ continue; }
            if($this->arg('layout')){
                if($node->type == 'YA'){
                    $sf = SchemeFulfillment::staticGet('nodeID', $node->nodeID);
                    if($sf){
                        if($sf->schemeID == 149 || $sf->schemeID == 150 || $sf->schemeID == 151 || $sf->schemeID == 152 || $sf->schemeID == 153){ continue; }
                    }
                }elseif($node->type == 'L'){
                    $edgeTo = $node->getNodesOutInSet($this->arg('nodesetid'));
                    while($edgeTo->fetch()){
                        $sf = SchemeFulfillment::staticGet('nodeID', $edgeTo->nodeID);
                        if($sf){
                            if($sf->schemeID == 149 || $sf->schemeID == 150 || $sf->schemeID == 151 || $sf->schemeID == 152 || $sf->schemeID == 153){ continue 2; }
                        }
                    }
                }
            }
            $nodes_out .= $this->show_node($node, $plus);
            if($node->type != 'I' && $node->type != 'L'){
                $edges = array_merge($edges, $this->show_edge($node, $plus));
            }
        }

        $edges_out = implode(" ", array_unique($edges));

        $dot_out = $nodes_out . $edges_out;
        return $dot_out;
    }

    function show_node($node) {
        $node_out = ' ';
        $node_out .= $node->nodeID;
        $node_out .= ' [label="';
        //$node_out .= wordwrap($node->text, 50, "XXXX");
        $node_out .= wordwrap(addslashes($node->text), 30, '\n');
        //$node_out .= addslashes($node->text);
        $node_out .= '"];';
        $node_out .= "\n";
        return $node_out;
    }

    function show_edge($node, $plus) {
        $edgeTo = $node->getNodesOutInSet($this->arg('nodesetid'));
        $edgeFrom = $node->getNodesInInSet($this->arg('nodesetid'));
        $edges = array();
        $ranks = array();
        while($edgeTo->fetch()){
            if(!$plus && $edgeTo->type != 'I' && $edgeTo->type != 'RA' && $edgeTo->type != 'CA'){ continue; }
            $edge_out = '';
            $edge_out .= $node->nodeID;
            $edge_out .= ' -> ';
            $edge_out .= $edgeTo->nodeID;
            if($plus && $this->arg('layout') && (($node->type != 'YA') && ($edgeTo->type != 'YA'))){
                if(($node->type == 'RA' || $node->type == 'CA') && $edgeTo->type == 'I'){
                    $ranks[] = '{ rank = same; '.$node->nodeID.'; '.$edgeTo->nodeID.'; }';
                }
                $edge_out .= ' [constraint=false]';
            }
            $edge_out .= ";";
            $edges[] = $edge_out;
        }
        while($edgeFrom->fetch()){
            if(!$plus && $edgeFrom->type != 'I' && $edgeFrom->type != 'RA' && $edgeFrom->type != 'CA'){ continue; }
            $edge_out = '';
            $edge_out .= $edgeFrom->nodeID;
            $edge_out .= ' -> ';
            $edge_out .= $node->nodeID;
            if($plus && $this->arg('layout') && ($node->type != 'YA' && $edgeFrom->type != 'YA')){
                if(($edgeFrom->type == 'RA' || $edgeFrom->type == 'CA') && $node->type == 'I'){
                    $ranks[] = '{ rank = same; '.$edgeFrom->nodeID.'; '.$node->nodeID.'; }';
                }
                $edge_out .= ' [constraint=false]';
            }
            $edge_out .= ";";
            $edges[] = $edge_out;
        }
        return array_merge($edges, $ranks);
    }
}
