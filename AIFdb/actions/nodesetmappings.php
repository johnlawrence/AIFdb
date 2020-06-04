<?php

class NodeSetMappingsAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            common_auth();

            $nodesetmapping_data = json_decode(file_get_contents("php://input"));

            $nodesetmapping = DB_DataObject::factory('nodesetmappings');
            assert($nodesetmapping);
            $nodesetmapping->nodeID = $nodesetmapping_data->{'nodeID'};
            $nodesetmapping->nodeSetID = $nodesetmapping_data->{'nodeSetID'};        
            $nodesetmappingId = $nodesetmapping->insert(); 

            $return = "";
            $return .= $nodesetmapping->encodeJSON();

            common_template('clean', '', $return);
        }elseif($this->arg('id')){
            $id = $this->arg('id');
            $nodesetmapping = NodeSetMappings::staticGet($id);

            if (!$nodesetmapping) {
                $this->no_such_nodesetmapping();
            }

            $return = "";
            $return .= $nodesetmapping->encodeJSON();

            common_template('clean', '', $return);
        }
    }

    function no_such_nodesetmapping() {
        common_user_error('No such nodesetmapping.');
    }

}
