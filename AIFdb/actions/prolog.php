<?php

class PrologAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($this->arg('multi')){
            $post_data = file_get_contents('php://input');
            $PL_nodes = array();
            $PL_edges = array();
            $insets = Nodes::getInSet($post_data);

            while($insets->fetch()){
                $PL_nodes[] = $this->show_node($insets);
            }

            $einsets = Edges::getInSet($post_data);
            while($einsets->fetch()){
                $PL_edges[] = $this->show_edge($einsets);
            }

            $node_output = implode("\n", $PL_nodes);
            $edge_output = implode("\n", $PL_edges);
            $output = $node_output . "\n" . $edge_output;

            common_template('plain', '', $output);
        }elseif($_SERVER['REQUEST_METHOD'] == 'POST') {
            $uploaddir = INSTALLDIR.'/tmp/';
            $pl_file = $uploaddir . basename($_FILES['file']['name']);

            if(move_uploaded_file($_FILES['file']['tmp_name'], $pl_file)){
                require_once(INSTALLDIR.'/lib/plparse.php');
                $data = file_get_contents($pl_file);

                $pl = new PLParser( $data );
                $nodeset = DB_DataObject::factory('nodesets');
                assert($nodeset);
                $nodesetId = $nodeset->insert();

                foreach ($pl->nodes as $node) {
                    $nodeID = $this->add_node($node);
                    $nodes_added['NA'.$node[id]] = $nodeID;
                    $nodesetmapping = DB_DataObject::factory('nodesetmappings');
                    assert($nodesetmapping);
                    $nodesetmapping->nodeID = $nodeID;
                    $nodesetmapping->nodeSetID = $nodesetId;
                    $nodesetmappingId = $nodesetmapping->insert();
                }

                foreach ($pl->edges as $edge) {
                    $edgeID = $this->add_edge($nodes_added['NA'.$edge['from']], $nodes_added['NA'.$edge['to']]);
                }

                $return = "Imported as nodeSet " . $nodesetId;
                common_template('clean', '', $return);
            }
        }elseif($this->arg('nodesetid')){
            $nodeset_id = $this->arg('nodesetid');
            $PL_nodes = array();
            $PL_edges = array();

            $nodeset = NodeSets::staticGet('nodeSetID', $nodeset_id);
            if (!$nodeset) {
                $this->no_such_nodeset();
                return;
            }
            $nodes = $nodeset->getNodes();
            $edges = $nodeset->getEdges();
            while($nodes->fetch()){
                $PL_nodes[] = $this->show_node($nodes);
            }
            while($edges->fetch()){
                $PL_edges[] = $this->show_edge($edges);
            }

            $node_output = implode("\n", $PL_nodes);
            $edge_output = implode("\n", $PL_edges);
            $output = $node_output . "\n" . $edge_output;

            common_template('plain', '', $output);
        }elseif($this->arg('nodesets')){
            $PL_nodes = array();
            $PL_edges = array();
            $insets = Nodes::getInSet($this->arg('nodesets'));

            while($insets->fetch()){
                $PL_nodes[] = $this->show_node($insets);
            }

            $einsets = Edges::getInSet($this->arg('nodesets'));
            while($einsets->fetch()){
                $PL_edges[] = $this->show_edge($einsets);
            }

            $node_output = implode("\n", $PL_nodes);
            $edge_output = implode("\n", $PL_edges);
            $output = $node_output . "\n" . $edge_output;

            common_template('plain', '', $output);
        }
    }

    function show_node($node) {
        $node_out = "aif_node(";
        $node_out.= $node->nodeID;
        $node_out.= ", ";
        if($node->type == 'I' || $node->type == 'L'){
            $node_out.= $this->clean_text($node->text);
        }else{
            $node_out.= "aif_";
            $s = $node->getScheme();
            if($s != ""){
                $node_out.= str_replace(" ", "_", $node->getScheme());
            }else{
                $node_out.= $node->type;
            }
        }
        $node_out.= ", ";
        $node_out.= "aif_" . strtoupper($node->type);
        $node_out.= ", ";
        $node_out.= "date(" . date("Y,m,d,H,i,s", strtotime($node->timestamp)) . ",-,-,-)";
        $node_out.= ").";
        
        return $node_out;
    }

    function show_edge($edge) {
        $edge_out = "aif_edge(";
        $edge_out.= $edge->fromID;
        $edge_out.= ", ";
        $edge_out.= $edge->toID;
        $edge_out.= ").";
         
        return $edge_out;
    }

    function show_edge2($f, $t){
        $edge_out = "aif_edge(";
        $edge_out.= $f;
        $edge_out.= ", ";
        $edge_out.= $t;
        $edge_out.= ").";

        return $edge_out;
    }

    function clean_text($t) {
        $i = array();
        $w = "";
        $uc = false;

        foreach(str_split($t) as $l){
            if(preg_match("/[a-zA-Z]/", $l)){
                if($w == "" && preg_match("/[A-Z]/", $l)){
                    $w .= "'";
                    $uc = true;
                }
                $w .= $l;
            }else{
                if($w != ""){
                    if($uc){ $w.="'"; $uc = false; }
                    $i[] = $w;
                    $w = "";
                }
                if($l == "'"){
                    $i[] = 'simplequote';
                }elseif(!preg_match('/\s/', $l)){
                    $i[] = "'" . $l . "'";
                }
            }
        }

        if($w != ""){
            if($uc){ $w.="'"; $uc = false; }
            $i[] = $w; 
        }

        $r = '[' . implode(',', $i) . ']';
        return $r;
    }

    function add_node($node_data) {
        $text = $node_data['text'];
        $type = $node_data['type'];
        $node_text = str_replace("\n", " ", trim($text));
        $existing_node = false;

        if(strtolower($type) != "ra" && strtolower($type) != "ca" && strtolower($type) != "ya" && strtolower($type) != "ta" && strtolower($type) != "ma" && strtolower($type) != "pa"){
            $existing_node = Nodes::staticGet('text', $node_text);
        }

        if($existing_node){
            $return = $existing_node->nodeID;
        }else{
            $node = DB_DataObject::factory('nodes');
            assert($node);
            $node->text = $node_text;
            $node->type = $type;
            $return = $node->insert();
        }

        return $return;
    }

    function add_edge($from, $to) {
        $edge = DB_DataObject::factory('edges');
        assert($edge);
        $edge->fromID = $from;
        $edge->toID = $to;
        $edgeId = $edge->insert();

        return $edgeId;
    }

    function no_such_nodeset() {
        common_user_error('No such nodeset');
    }
}
