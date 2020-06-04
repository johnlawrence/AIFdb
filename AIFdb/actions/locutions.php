<?php

class locutionsAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            common_auth();

            $locution_data = json_decode(file_get_contents("php://input"));

            $locution = DB_DataObject::factory('locutions');
            assert($locution);
            $locution->nodeID = $locution_data->{'nodeID'};
            $locution->personID = $locution_data->{'personID'};
            if(isset($locution_data->{'timestamp'})){
                $locution->timestamp = $locution_data->{'timestamp'};
            }else{
                $locution->timestamp = date("Y-m-d H:i:s", time());
            }
            if(isset($locution_data->{'start'})){
                $locution->start = $locution_data->{'start'};
            }
            if(isset($locution_data->{'end'})){
                $locution->end = $locution_data->{'end'};
            }
            $locution->source = $locution_data->{'source'};
            $locution->insert(); 
            $return = "";
            $return .= $locution->encodeJSON();
            
            common_template('clean', 'return', $return);
        }else{
            $id = $this->arg('id');
            
            if($this->arg('bynode')){
                $locs = new Locutions;
                $locs->query("SELECT * FROM locutions WHERE nodeID = ".$id.";");
                $return = '{"locutions":[';
                $JSON = array();
                while($locs->fetch()){
                    $JSON[] = $locs->encodeJSON();
                }
                $return = $return . implode(',', $JSON);
                $return = $return . ']}';
                common_template('clean', '', $return);
            }elseif($this->arg('bynodeset')){
                $locs = new Locutions;
                $locs->query("SELECT * FROM locutions INNER JOIN nodeSetMappings ON ( locutions.nodeID = nodeSetMappings.nodeID ) WHERE nodeSetMappings.nodeSetID = ".$id.";");
                $return = '{"locutions":[';
                $JSON = array();
                while($locs->fetch()){
                    $JSON[] = $locs->encodeJSON();
                }
                $return = $return . implode(',', $JSON);
                $return = $return . ']}';
                common_template('clean', '', $return);
            }else{
                $nodes = new Nodes;
                $nodes->query("SELECT nodes.* FROM nodes INNER JOIN locutions ON ( locutions.nodeID = nodes.nodeID ) WHERE locutions.personID = ".$id.";");
                $return = '{"locutions":[';
                $JSON = array();
                while($nodes->fetch()){
                    $ya = $nodes->getNodesOut();
                    while($ya->fetch()){
                        $i = $ya->getNodesOut();
                        while($i->fetch()){
                            if(isset($i->source)){
                                $JSON[] = '{"nodeID":"'.$i->nodeID.'","text":"'.$i->text.'","timestamp":"'.$nodes->timestamp.'","source":"'.$i->source.'"}';
                            }else{
                                $JSON[] = '{"nodeID":"'.$i->nodeID.'","text":"'.$i->text.'","timestamp":"'.$nodes->timestamp.'","source":""}';
                            }
                        }
                    }
                }
                $return = $return . implode(',', $JSON);
                $return = $return . ']}';
            }

            common_template('clean', $title, $return);
        }
    }

    function no_such_locution() {
        common_user_error('No such locution.');
        exit;
    }
}
