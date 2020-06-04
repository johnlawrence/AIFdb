<?php

class AmlAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {

            $uploaddir = INSTALLDIR.'/tmp/';
            $aml_file = $uploaddir . basename($_FILES['file']['name']);

            if(move_uploaded_file($_FILES['file']['tmp_name'], $aml_file)){
                require_once(INSTALLDIR.'/lib/amlparse.php');
                $data = file_get_contents($aml_file);

                $aml = new amlParser( $data );
                $nodeset = DB_DataObject::factory('nodesets');
                assert($nodeset);
                $nodesetId = $nodeset->insert();

                foreach ($aml->nodes as $node) {
                    $nodeID = $this->add_node($node);
                    $nodes_added['NA'.$node[id]] = $nodeID;
                    $nodesetmapping = DB_DataObject::factory('nodesetmappings');
                    assert($nodesetmapping);
                    $nodesetmapping->nodeID = $nodeID;
                    $nodesetmapping->nodeSetID = $nodesetId;
                    $nodesetmappingId = $nodesetmapping->insert();
                }

                foreach ($aml->edges as $edge) {
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

            $output = $this->generate_aml($nodeset);

	        common_template('aml', '', $output);
	    }
    }

    function no_such_nodeset() {
        common_user_error('No such nodeset');
    }

    function generate_aml($nodeset) {
        $node = $nodeset->getNodes();
        global $mapid, $ids, $inf;
        $s = '';
        $ids = array();
        $inf = array();

        //find the 'root'
        while($node->fetch()){
            $out = $node->getNodesOut();
            if(!$out->fetch() && $node->type == 'I'){
                $aml_out .= $this->list_children($node, $s);
            }
        }
        return $aml_out;
    }

    function list_children($node, $s) {
        $s .= '    ';
        $c_out = $s.'<AU>'."\n";
        $c_end = $s.'</AU>'."\n";
        $s .= '    ';
        $c_out .= $s.'<PROP identifier="n'.$node->nodeID.'" missing="yes">'."\n";
        $c_out .= $s.'    <PROPTEXT offset="-1">'.$node->text.'</PROPTEXT>'."\n";
        $c_out .= $s.'    <OWNER name="VCBrown" />'."\n";
        $c_out .= $s.'</PROP>'."\n";

        $child = $node->getNodesIn();
        while($child->fetch()){
            if($child->type == 'RA'){
                $i_to = $child->getNodesIn();
                $i_count = 0;
                $s_out = '';
                while($i_to->fetch()){
                    if($i_to->type == 'I'){
                        $s_out .= $this->list_children($i_to, $s);
                        $i_count++;
                    }
                }
                if($i_count > 1){
                    $c_out .= $s.'<LA>'."\n".$s_out.$s.'</LA>'."\n";
                }else{
                    $c_out .= $s.'<CA>'."\n".$s_out.$s.'</CA>'."\n";
                }
            }
        }

        $c_out .= $c_end;

        return $c_out;
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
