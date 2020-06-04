<?php

class RtnlAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {

            $uploaddir = INSTALLDIR.'/tmp/';
            $rtnl_file = $uploaddir . basename($_FILES['file']['name']);

            if(move_uploaded_file($_FILES['file']['tmp_name'], $rtnl_file)){
                require_once(INSTALLDIR.'/lib/rtnlparse.php');
                $data = file_get_contents($rtnl_file);

                $rtnl = new rtnlParser( $data );
                $nodeset = DB_DataObject::factory('nodesets');
                assert($nodeset);
                $nodesetId = $nodeset->insert();

                foreach ($rtnl->nodes as $node) {
                    $nodeID = $this->add_node($node);
                    $nodes_added['NA'.$node[id]] = $nodeID;
                    $nodesetmapping = DB_DataObject::factory('nodesetmappings');
                    assert($nodesetmapping);
                    $nodesetmapping->nodeID = $nodeID;
                    $nodesetmapping->nodeSetID = $nodesetId;
                    $nodesetmappingId = $nodesetmapping->insert();
                }

                foreach ($rtnl->edges as $edge) {
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

            $output = $this->generate_rtnl($nodeset);

	        common_template('rtnl', '', $output, array('nodeset' => $nodeset_id));
	    }
    }

    function no_such_nodeset() {
        common_user_error('No such nodeset');
    }

    function generate_rtnl($nodeset) {
        $node = $nodeset->getNodes();
        $nodeSetID = $nodeset->nodeSetID;
        global $mapid, $ids, $inf, $mapidx;
        $mapidx = 0;
        $mapid = 0;
        $ids = array();
        $inf = array();
        $rtnl_out = "";

        //find the 'root'
        while($node->fetch()){
            $out = $node->getNodesOutInSet($nodeSetID);
            $mapid = 0;
            if(!$out->fetch() && $node->type == 'I'){
                $ids[$node->nodeID] = 'map' . $mapidx . '_' . $mapid;
                $mapid++;
                $mapidx++;
                $rtnl_out .= $ids[$node->nodeID].' = Create("Claim")'."\n";
                $rtnl_out .= 'SetText("'.$node->text.'")'."\n";
                $rtnl_out .= $this->list_children($node, $nodeSetID);
            }
        }
        return $rtnl_out;
    }

    function list_children($node, $nodeSetID) {
        global $mapid, $ids, $inf, $mapidx;

        $child = $node->getNodesInInSet($nodeSetID);
        $c_out = '';
        while($child->fetch()){
            //need an inference to link to
            if($node->type != 'I' && ($child->type=='RA' || $child->type=='CA') && !isset($inf[$node->nodeID])){
                $inf[$node->nodeID] = 'map' . $mapidx . '_'.$mapid;
                $mapid++;
                $c_out .= $inf[$node->nodeID].' = CreateChild('.$ids[$node->nodeID].', "Inference")'."\n";
            }

            if(!isset($ids[$child->nodeID])){
                $ids[$child->nodeID] = 'map' . $mapidx . '_'.$mapid; 
                $mapid++;
            }
            switch ($child->type) {
                case 'RA' :
                    $target = ($node->type != 'I') ? $inf[$node->nodeID] : $node->nodeID;
                    $c_out .= $ids[$child->nodeID].' = CreateChild('.$ids[$target].', "CompoundReason")'."\n";
                    break;
                case 'CA':
                    $target = ($node->type != 'I') ? $inf[$node->nodeID] : $node->nodeID;
                    $c_out .= $ids[$child->nodeID].' = CreateChild('.$ids[$node->nodeID].', "CompoundObjection")'."\n";
                    break;
                case 'I':
                    $c_out .= $ids[$child->nodeID].' = CreateChild('.$ids[$node->nodeID].', "Claim")'."\n";
                    $c_out .= 'SetText("'.$child->text.'")'."\n";
                    break;
            }

            $c_out .= $this->list_children($child, $nodeSetID);

        }
        //add unused inference to match rtnl output
        if(($node->type == 'RA' || $node->type == 'CA') && !isset($inf[$node->nodeID])){
            $inf[$node->nodeID] = 'map' . $mapidx . '_'.$mapid;
            $mapid++;
            $c_out .= $inf[$node->nodeID].' = CreateChild('.$ids[$node->nodeID].', "Inference")'."\n";
        }
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
