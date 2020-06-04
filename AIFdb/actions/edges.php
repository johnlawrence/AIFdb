<?php

class EdgesAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            common_auth();

            $edge_data = json_decode(file_get_contents("php://input"));

            $edge = DB_DataObject::factory('edges');
            assert($edge);
            $edge->fromID = $edge_data->{'fromID'};
            $edge->toID = $edge_data->{'toID'};        
            $edge->formEdgeID = $edge_data->{'formEdgeID'};
            if($edge->find()){
                $edge->fetch();
                $edgeId = $edge->{'edgeID'};
            }else{
                $edgeId = $edge->insert(); 
            }

            $return = "";
            $return .= $edge->encodeJSON();

            common_template('clean', '', $return);
        }elseif($this->arg('id')){
            $id = $this->arg('id');
            $edge = Edges::staticGet($id);

            if (!$edge) {
                $this->no_such_edge();
            }

            $return = "";
            $return .= $edge->encodeJSON();

            common_template('clean', '', $return);
        }elseif($this->arg('to')){
            $edges = DB_DataObject::factory('edges');
            $edges->toID = $this->arg('to');
            $edges->orderBy('edgeID ASC');

            $cnt = $edges->find();

            $return = '{"edges":[';
            $JSON = array();
            while($edges->fetch()){
                $JSON[] = $edges->encodeJSON();
            }
            $return = $return . implode(',', $JSON);
            $return = $return . ']}';

            common_template('clean', '', $return);
        }elseif($this->arg('from')){
            $edges = DB_DataObject::factory('edges');
            $edges->fromID = $this->arg('from');
            $edges->orderBy('edgeID ASC');

            $cnt = $edges->find();

            $return = '{"edges":[';
            $JSON = array();
            while($edges->fetch()){
                $JSON[] = $edges->encodeJSON();
            }
            $return = $return . implode(',', $JSON);
            $return = $return . ']}';

            common_template('clean', '', $return);
        }
    }

    function no_such_edge() {
        common_user_error('No such edge.');
    }
}
