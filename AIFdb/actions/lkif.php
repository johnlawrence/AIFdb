<?php

class LkifAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {

            $uploaddir = INSTALLDIR.'/tmp/';
            $lkif_file = $uploaddir . basename($_FILES['file']['name']);

            if(move_uploaded_file($_FILES['file']['tmp_name'], $lkif_file)){
                require_once(INSTALLDIR.'/lib/lkifparse.php');
                $data = file_get_contents($lkif_file);

                $lkif = new lkifParser( $data );

                $nodeset = DB_DataObject::factory('nodesets');
                assert($nodeset);
                $nodesetId = $nodeset->insert();

                foreach ($lkif->nodes as $node) {
                    $nodeID = $this->add_node($node);
                    $nodes_added['NA'.$node[id]] = $nodeID;
                    $nodesetmapping = DB_DataObject::factory('nodesetmappings');
                    assert($nodesetmapping);
                    $nodesetmapping->nodeID = $nodeID;
                    $nodesetmapping->nodeSetID = $nodesetId;
                    $nodesetmappingId = $nodesetmapping->insert();
                }

                foreach ($lkif->edges as $edge) {
                    $edgeID = $this->add_edge($nodes_added['NA'.$edge['from']], $nodes_added['NA'.$edge['to']]);
                }

                $return = "Imported as nodeSet " . $nodesetId;
                common_template('clean', '', $return);
                
            }else{
	            common_user_error('Error uploading file');
            }

        }else{
	        $nodeset_id = $this->arg('nodesetid');
            $nodeset = NodeSets::staticGet('nodeSetID', $nodeset_id);

            if (!$nodeset) {
                $this->no_such_nodeset();
                return;
            }

            $output = $this->generate_lkif($nodeset);

	        common_template('lkif', $title, $output);
	    }
    }

    function no_such_nodeset() {
        common_user_error('No such nodeset');
    }

    function generate_lkif($nodeset) {
        $node = $nodeset->getNodes();
        
        $s = "            ";
        $statements_out = $s."<statements>";
        $arguments_out = $s."<arguments>";

        while($node->fetch()){
            if($node->type == 'I'){
                $statements_out .= $this->show_statement($node, $s);
            }else{
                $arguments_out .= $this->show_argument($node, $s);
            }
        }

        $statements_out .= $s."</statements>";
        $arguments_out .= $s."</arguments>";

        $lkif_out = $statements_out . "\n" . $arguments_out . "\n";
        return $lkif_out;
    }

    function show_statement($node, $s) {
        $statement_out = "\n";
        $statement_out .= $s.'    <statement assumption="true" id="' . $node->nodeID . '" standard="BA" value="unknown">' . "\n";
        $statement_out .= $s.'        <s>' . $node->text . '</s>' . "\n";
        $statement_out .= $s.'    </statement>' . "\n";

        return $statement_out;
    }

    function show_argument($node, $s) {
        $conclusion = $node->getNodesOut();
        $premise = $node->getNodesIn();
        $conclusion->fetch();

        $argument_out = '';

        if($conclusion->type == 'I'){
            $argument_out = "\n";
            if($node->type == 'CA'){
                $argument_out .= $s.'    <argument direction="con" id="';
            }else{
                $argument_out .= $s.'    <argument direction="pro" id="';
            }
            $argument_out .= $node->nodeID . '" scheme="' . $node->text . '" weight="0.5">' . "\n";
            $argument_out .= $s.'        <conclusion statement="' . $conclusion->nodeID . '"/>' . "\n";
            $argument_out .= $s.'        <premises>' . "\n";
            while($premise->fetch()){
                if($premise->type == 'I'){
                    $argument_out .= $s.'            <premise polarity="positive" type="ordinary"';
                    $argument_out .= ' role="" statement="' . $premise->nodeID . '"/>' . "\n";
                }else{
                    $p_root = $premise->getNodesIn();
                    $p_type = ($premise->type == 'CA') ? 'exception' : 'assumption';
                    while($p_root->fetch()){
                        if($p_root->type == 'I'){
                            $argument_out .= $s.'            <premise polarity="positive" type="';
                            $argument_out .= $p_type.'" role="" statement="' . $p_root->nodeID . '"/>' . "\n";
                        }
                    }
                }
            }
            $argument_out .= $s.'        </premises>' . "\n";
            $argument_out .= $s.'    </argument>' . "\n";
        }
        return $argument_out;
    }


    function add_node($node_data) {
        $props = array("text", "type"); 
        $node = DB_DataObject::factory('nodes');
        assert($node);
        
        foreach ($node_data as $key => $value){
            if(in_array($key, $props)){
                $node->$key = $value;
            }
        }
        $nodeId = $node->insert(); 

        return $nodeId; 
    }

    function add_edge($from, $to) {
        $edge = DB_DataObject::factory('edges');
        assert($edge);
        $edge->fromID = $from;
        $edge->toID = $to;
        $edgeId = $edge->insert(); 

        return $edgeId;
    }
}
