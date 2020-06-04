<?php

class descriptorFulfillmentAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            common_auth();

            $descriptorFulfillment_data = json_decode(file_get_contents("php://input"));

            $descriptorFulfillment = DB_DataObject::factory('descriptorFulfillment');
            assert($descriptorFulfillment);
            $descriptorFulfillment->nodeID = $descriptorFulfillment_data->{'nodeID'};
            $descriptorFulfillment->descriptorID = $descriptorFulfillment_data->{'descriptorID'};        
            $descriptorFulfillmentId = $descriptorFulfillment->insert(); 
            $return = "";
            $return .= $descriptorFulfillment->encodeJSON();
            
            common_template('clean', 'return', $return);
        }else{
            $id = $this->arg('id');
            $descriptorFulfillment = descriptorFulfillment::staticGet($id);

            if (!$descriptorFulfillment) {
                $this->no_such_descriptorFulfillment();
            }

            $return = "";
            $return .= $descriptorFulfillment->encodeJSON();

            common_template('clean', $title, $return);
        }
    }

    function no_such_descriptorFulfillment() {
        common_user_error('No such descriptorFulfillment.');
        exit;
    }
}
