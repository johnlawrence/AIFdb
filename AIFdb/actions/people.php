<?php

class PeopleAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            common_auth();

            $person_data = json_decode(file_get_contents("php://input"));

            $person = DB_DataObject::factory('people');
            assert($person);
            $person->firstName = $person_data->{'firstName'};
            $person->surname = $person_data->{'surname'};
            $person->description = $person_data->{'description'};
            $personId = $person->insert(); 
            $return = "";
            $return .= $person->encodeJSON();
            
            common_template('clean', 'return', $return);
        }else{
            $id = $this->arg('id');
            $person = People::staticGet($id);

            if (!$person) {
                $this->no_such_person();
            }

            $return = "";
            $return .= $person->encodeJSON();

            common_template('clean', '', $return);
        }
    }

    function no_such_person() {
        common_user_error('No such person.');
        exit;
    }
}
