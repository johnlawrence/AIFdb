<?php

class DiagramAction extends Action {
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
            if($this->arg('plus')){
                $output .= '    rankdir=RL;'."\n";
            }
            if($this->arg('flip')){
                $output .= '    rankdir=BT;'."\n";
            }
            $output .= "    graph [fontsize=10];";
            $output .= "    edge  [fontsize=10];";
            $output .= "    node  [fontsize=10];";
            $output .= $this->generate_dot($nodeset);
            $output .= "}";

            if($this->arg('type') == 'svg'){
                common_template('svg', '', $output, array('nodeset' => $nodeset_id));
            }else{
                common_template('png', '', $output, array('nodeset' => $nodeset_id));
            }
        }elseif($this->arg('nodesets')){
            $plus = false;
            if($this->arg('plus')){
                $plus = true;
            }
            $DG_nodes = array();
            $DG_edges = array();
            $snodes = array();
            $insets = Nodes::getInSet($this->arg('nodesets'));

            while($insets->fetch()){
                if (preg_match("/\s*\w+(?: \w+)?\s*:.{0,30}:/", $insets->text)) {
                }else{
                    if($insets->type == 'RA' || $insets->type == 'CA' || $insets->type == 'MA' || $insets->type == 'PA'){
                        $edgeTo = $insets->getNodesOut();
                        $edgeFrom = $insets->getNodesIn();
                        $et = '';
                        $ef = '';
                        while($edgeTo->fetch()){
                            $et .= $edgeTo->nodeID . '-';
                        }
                        while($edgeFrom->fetch()){
                            $ef .= $edgeFrom->nodeID . '-';
                        }
                        $eft = $ef.'>'.$et;
                        if(in_array($eft, $snodes)){
                            continue;
                        }else{
                            $snodes[] = $eft;
                        }
                    }

                    $ndot = $this->show_node($insets, $plus);
                    if($ndot != ''){
                        $DG_nodes[$insets->nodeID] = $ndot;
                    }
                }
            }

            $einsets = Edges::getInSet($this->arg('nodesets'));
            while($einsets->fetch()){
                $DG_edges[] = $this->show_edge2($einsets, true, $DG_nodes);
            }

            $output = "digraph nodeset" . $nodeset_id . " {";
            if($this->arg('flip')){
                $output .= '    rankdir=BT;'."\n";
            }
            $output .= "    graph [fontsize=10];";
            $output .= "    edge  [fontsize=10];";
            $output .= "    node  [fontsize=10];";
            $output .= implode(" ", $DG_nodes);
            $output .= implode(" ", $DG_edges);
            $output .= "}";

            if($this->arg('type') == 'svg'){
                common_template('svg', '', $output, array('nodeset' => 'multi'));
            }else{
                common_template('png', '', $output, array('nodeset' => 'multi'));
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
            if(!$plus && $node->type != 'I' && $node->type != 'RA' && $node->type != 'CA' && $node->type != 'MA' && $node->type != 'PA'){ continue; }
            $nodes_out .= $this->show_node($node, $plus);
            if($node->type != 'I' && $node->type != 'L'){
                $edges = array_merge($edges, $this->show_edge($node, $plus));
            }
        }

        $edges_out = implode(" ", array_unique($edges));

        $dot_out = $nodes_out . $edges_out;
        return $dot_out;
    }

    function show_node($node, $plus) {
        if(!$plus && $node->type != 'I' && $node->type != 'RA' && $node->type != 'CA' && $node->type != 'MA' && $node->type != 'PA'){
            return '';
        }
        $node_out = ' ';
        $node_out .= $node->nodeID;
        $node_out .= ' [label="';
        if($node->type=="I" || $node->type=="L"){
            $node_out .= wordwrap(addslashes($node->text), 30, '\n');
        }elseif($node->name != NULL){
            $node_out .= $node->name;
        }else{
            $node_out .= $node->text;
        }
        $node_out .= '", shape="';
        $node_out .= ($node->type=="I" || $node->type=="L") ? 'box' : 'diamond';
        $node_out .= '", style=filled, ';
        if($node->type=="I" || $node->type=="L"){
            $node_out .= 'fontname="FreeSans", color="#6666cc", fillcolor="#ebf3ff"';
        }elseif($node->type=="CA"){
            $node_out .= 'fontname="FreeSans", color="#cc6666", fillcolor="#ffe6e6"';
        }elseif($node->type=="RA"){
            $node_out .= 'fontname="FreeSans", color="#66cc66", fillcolor="#e6ffe6"';
        }elseif($node->type=="YA"){
            $node_out .= 'fontname="FreeSans", color="#cccc66", fillcolor="#ffffe6"';
        }elseif($node->type=="TA"){
            $node_out .= 'fontname="FreeSans", color="#cc66cc", fillcolor="#ffe6ff"';
        }else{
            $node_out .= 'fontname="FreeSans", color="#cccccc", fillcolor="#eeeeee"';
        }
        $node_out .= '];';
        $node_out .= "";
        return $node_out;
    }

    function show_edge2($edge, $plus, $DG_nodes) {
        if(!array_key_exists($edge->fromID, $DG_nodes) || !array_key_exists($edge->toID, $DG_nodes)){
            return '';
        }
        $edge_out = '';
        $edge_out .= $edge->fromID;
        $edge_out .= ' -> ';
        $edge_out .= $edge->toID;
        $edge_out.= ";";

        return $edge_out;
    }

    function show_edge($node, $plus) {
        $edgeTo = $node->getNodesOutInSet($this->arg('nodesetid'));
        $edgeFrom = $node->getNodesInInSet($this->arg('nodesetid'));
        $edges = array();
        $ranks = array();
        while($edgeTo->fetch()){
            if(!$plus && $edgeTo->type != 'I' && $edgeTo->type != 'RA' && $edgeTo->type != 'CA' && $edgeTo->type != 'MA' && $edgeTo->type != 'PA'){ continue; }
            $edge_out = '';
            $edge_out .= $node->nodeID;
            $edge_out .= ' -> ';
            $edge_out .= $edgeTo->nodeID;
            if($plus && (($node->type != 'YA') && ($edgeTo->type != 'YA'))){
                if(($node->type == 'RA' || $node->type == 'CA' || $node->type == 'MA' || $node->type == 'PA') && $edgeTo->type == 'I'){
                    $ranks[] = '{ rank = same; '.$node->nodeID.'; '.$edgeTo->nodeID.'; }';
                }
                $edge_out .= ' [constraint=false]';
            }
            //$edge_out .= '[color="#444444"]';
            $edge_out .= ";";
            $edges[] = $edge_out;
        }
        while($edgeFrom->fetch()){
            if(!$plus && $edgeFrom->type != 'I' && $edgeFrom->type != 'RA' && $edgeFrom->type != 'CA' && $edgeFrom->type != 'MA' && $edgeFrom->type != 'PA'){ continue; }
            $edge_out = '';
            $edge_out .= $edgeFrom->nodeID;
            $edge_out .= ' -> ';
            $edge_out .= $node->nodeID;
            if($plus && ($node->type != 'YA' && $edgeFrom->type != 'YA')){
                if(($edgeFrom->type == 'RA' || $edgeFrom->type == 'CA' || $edgeFrom->type == 'MA' || $edgeFrom->type == 'PA') && $node->type == 'I'){
                    $ranks[] = '{ rank = same; '.$edgeFrom->nodeID.'; '.$node->nodeID.'; }';
                }
                $edge_out .= ' [constraint=false]';
            }
            //$edge_out .= '[color="#444444"]';
            $edge_out .= ";";
            $edges[] = $edge_out;
        }
        return $edges;
    }
}
