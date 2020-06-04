<?php

class formedgesAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            common_auth();

            $formedge_data = json_decode(file_get_contents("php://input"));

            $formedge = DB_DataObject::factory('formedges');
            assert($formedge);
            $formedge->schemeID = $formedge_data->{'schemeID'};
            $formedge->descriptorID = $formedge_data->{'descriptorID'};
            $formedge->descriptorType = $formedge_data->{'descriptorType'};
            $formedge->schemeTarget = $formedge_data->{'schemeTarget'};
            
            $formedgeId = $formedge->insert(); 
            $return = "";
            $return .= $formedge->encodeJSON();
            
            common_template('clean', '', $return);

        }elseif($this->arg('schemeID')){
            $formedges = new formEdges;
            $formedges->whereAdd("schemeID=".$this->arg('schemeID'));
            $cnt = $formedges->find();
            
            $return = '{"formedges":[';
            $JSON = array();
            while($formedges->fetch()){
                $JSON[] = $formedges->encodeJSON();
            }
            $return = $return . implode(',', $JSON);
            $return = $return . ']}';

            common_template('clean', '', $return);
        }else{
            $id = $this->arg('id');
            $formedge = formedges::staticGet($id);

            if (!$formedge) {
                $this->no_such_formedge();
            }

            $return = "";
            $return .= $formedge->encodeJSON();

            common_template('clean', '', $return);
        }
    }

    function no_such_formedge() {
        common_user_error('No such formedge.');
        exit;
    }
}
