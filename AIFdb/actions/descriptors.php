<?php

class descriptorsAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            common_auth();

            $descriptor_data = json_decode(file_get_contents("php://input"));

            $existing_descriptor = descriptors::staticGet('text', $descriptor_data->{'text'});

            if($existing_descriptor){
                $return = $existing_descriptor->encodeJSON();
            }else{
                $descriptor = DB_DataObject::factory('descriptors');
                assert($descriptor);
                $descriptor->text = $descriptor_data->{'text'};        
                $descriptorId = $descriptor->insert(); 
                $return = "";
                $return .= $descriptor->encodeJSON();
            }
            common_template('clean', 'return', $return);
        }else{
            $id = $this->arg('id');
            $descriptor = descriptors::staticGet($id);

            if (!$descriptor) {
                $this->no_such_descriptor();
            }

            $return = "";
            $return .= $descriptor->encodeJSON();

            common_template('clean', $title, $return);
        }
    }

    function no_such_descriptor() {
        common_user_error('No such descriptor.');
        exit;
    }
}
