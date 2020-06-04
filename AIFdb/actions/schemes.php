<?php

class schemesAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($_SERVER['REQUEST_METHOD'] == 'POST' && $this->arg('search')) {
            $scheme_data = json_decode(file_get_contents("php://input"));

            $existing_scheme = schemes::staticGet('name', $scheme_data->{'name'});
            if($existing_scheme){
                $return = $existing_scheme->encodeJSON();
            }
            common_template('clean', 'return', $return);
        }elseif($_SERVER['REQUEST_METHOD'] == 'POST') {
            common_auth();

            $scheme_data = json_decode(file_get_contents("php://input"));

            $existing_scheme = schemes::staticGet('name', $scheme_data->{'name'});

            if($existing_scheme){
                $return = $existing_scheme->encodeJSON();
            }else{
                $scheme = DB_DataObject::factory('schemes');
                assert($scheme);
                $scheme->name = $scheme_data->{'name'};
                $scheme->schemeTypeID = $scheme_data->{'schemeTypeID'};        
                $schemeId = $scheme->insert(); 
                $return = "";
                $return .= $scheme->encodeJSON();
            }
            common_template('clean', 'return', $return);
        }elseif($this->arg('all')){
            $schemes = new schemes;
            $cnt = $schemes->find();
            
            $return = '{"schemes":[';
            $JSON = array();
            while($schemes->fetch()){
                $JSON[] = $schemes->encodeJSON();
            }
            $return = $return . implode(',', $JSON);
            $return = $return . ']}';

            common_template('clean', '', $return);

        }else{
            $id = $this->arg('id');
            $scheme = schemes::staticGet($id);

            if (!$scheme) {
                $this->no_such_scheme();
            }

            $return = "";
            $return .= $scheme->encodeJSON();

            common_template('clean', $title, $return);
        }
    }

    function no_such_scheme() {
        common_user_error('No such scheme.');
        exit;
    }
}
