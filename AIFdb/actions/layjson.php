<?php

class LayjsonAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($this->arg('nodesetid')){
            $nodeSetID = $this->arg('nodesetid');
            if($_GET['plus'] == 'true' || true){
                $dot = file_get_contents('http://www.aifdb.org/index.php?action=dot&layout=1&nodesetid=' . $nodeSetID . '&plus=true');
            }else{
                $dot = file_get_contents('http://www.aifdb.org/index.php?action=dot&layout=1&nodesetid=' . $nodeSetID);
            }

            $dot = preg_replace('/\\\\\n/', '', $dot);

            $n = array();
            foreach(preg_split("/((\r?\n)|(\r\n?))/", $dot) as $line){
                if(preg_match('/^\s*([0-9]*)\s*\[label.*pos="([0-9\.]*),([0-9\.]*)"/', $line, $m)){
                    $n[] = '"' . $m[1] . '":{"x":"' . $m[2] . '","y":"' . $m[3] . '"}';
                }
            }

            $return = '{';
            $return = $return . implode(',', $n);
            $return = $return . '}';

            common_template('clean', '', $return);

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
