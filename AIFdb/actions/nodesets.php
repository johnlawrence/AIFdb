<?php

class NodeSetsAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            common_auth();

            if($this->arg('merge')){
                $merge_data = json_decode(file_get_contents("php://input"));
                $sql = "SELECT * FROM nodeSetMappings WHERE nodeSetID IN (" . implode(',', $merge_data->nodeSets) . ") group by nodeID order by nodeID ASC;";
                $nodesetmappings = new NodeSetMappings;
                $nodesetmappings->query($sql);

                $nodeset = DB_DataObject::factory('nodesets');
                assert($nodeset);
                $nodeset->title = $nodeset_data->{'title'};
                $nodesetId = $nodeset->insert();
                $return = "";
                $return .= $nodeset->encodeJSON();

                while($nodesetmappings->fetch()){
                    $nodesetmapping = DB_DataObject::factory('nodesetmappings');
                    assert($nodesetmapping);
                    $nodesetmapping->nodeID = $nodesetmappings->nodeID;
                    $nodesetmapping->nodeSetID = $nodesetId;
                    $nodesetmappingId = $nodesetmapping->insert();
                }
                common_template('clean', 'return', $return);
            }else{
                $nodeset_data = json_decode(file_get_contents("php://input"));

                $existing_nodeset = NodeSets::staticGet('title', $nodeset_data->{'title'});

                if($existing_nodeset){
                    $return = preg_replace( "/\"(\d+)\"/", '$1', $existing_nodeset->encodeJSON());
                }else{
                    $nodeset = DB_DataObject::factory('nodesets');
                    assert($nodeset);
                    $nodeset->title = $nodeset_data->{'title'};
                    $nodesetId = $nodeset->insert(); 
                    $return = "";
                    $return .= $nodeset->encodeJSON();
                }
                common_template('clean', 'return', $return);
            }
        }elseif($this->arg('id')){
            $id = $this->arg('id');
            $nodeset = NodeSets::staticGet($id);

            if (!$nodeset) {
                $this->no_such_nodeset();
            }

            $nodes = $nodeset->getNodes();
       
            if($this->arg('t')){
                $return = '{"nodes":[';
            }else{
                $return = '';
            }
            $JSON = array();
            while($nodes->fetch()){
                $JSON[] = $nodes->encodeJSON();
            }
            $return = $return . implode(',', $JSON);

            if($this->arg('t')){
                $return = $return . ']}';
            }

            common_template('clean', '', $return);
        }elseif($this->arg('nodeid')){
            $nodeID = $this->arg('nodeid');
            $nodeSets = new nodeSets;
            $nodeSetMappings = new nodeSetMappings;
            $nodeSets->joinAdd($nodeSetMappings);
            $nodeSets->whereAdd("nodeSetMappings.nodeID='$nodeID'");
            $ns_cnt = $nodeSets->find();
            
            $return = '{"nodeSets":[';
            $JSON = array();
            while($nodeSets->fetch()){
                $JSON[] = $nodeSets->encodeJSON();
            }
            $return = $return . implode(',', $JSON);
            $return = $return . ']}';

            common_template('clean', '', $return);
        }elseif($this->arg('new')){
            common_auth();

            $nodeset = DB_DataObject::factory('nodesets');
            assert($nodeset);
            $nodesetId = $nodeset->insert();

            $return = "";
            $return .= $nodeset->encodeJSON(); 

            common_template('clean', '', $return);
        }elseif($this->arg('merge')){
            common_redirect('../static/mergenodesets.php');            
        }
    }

    function no_such_nodeset() {
        common_user_error('No such nodeset.');
        exit;
    }

}
