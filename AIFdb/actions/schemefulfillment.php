<?php

class schemeFulfillmentAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            common_auth();

            $schemeFulfillment_data = json_decode(file_get_contents("php://input"));

            $schemeFulfillment = DB_DataObject::factory('schemeFulfillment');
            assert($schemeFulfillment);
            $schemeFulfillment->nodeID = $schemeFulfillment_data->{'nodeID'};
            $schemeFulfillment->schemeID = $schemeFulfillment_data->{'schemeID'};        
            $schemeFulfillmentId = $schemeFulfillment->insert(); 
            $return = "";
            $return .= $schemeFulfillment->encodeJSON();
            
            common_template('clean', 'return', $return);
        }else{
            $id = $this->arg('id');
            $schemeFulfillment = schemeFulfillment::staticGet($id);

            if (!$schemeFulfillment) {
                $this->no_such_schemeFulfillment();
            }

            $return = "";
            $return .= $schemeFulfillment->encodeJSON();

            common_template('clean', $title, $return);
        }
    }

    function no_such_schemeFulfillment() {
        common_user_error('No such schemeFulfillment.');
        exit;
    }
}
