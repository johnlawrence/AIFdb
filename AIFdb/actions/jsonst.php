<?php

class JsonstAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $uploaddir = INSTALLDIR.'/tmp/';
            $json_file = $uploaddir . basename($_FILES['file']['name']);

            if(move_uploaded_file($_FILES['file']['tmp_name'], $json_file)){
                $jsondata = file_get_contents($json_file);
                $json = json_decode($jsondata);

                $nodeset = DB_DataObject::factory('nodesets');
                assert($nodeset);
                $nodesetId = $nodeset->insert();
                foreach ($json->nodes as $node) {
                    $nodeID = $this->add_node($node->text, $node->type);
                    $nodes_added['NA'.$node->nodeID] = $nodeID;
                    $nodesetmapping = DB_DataObject::factory('nodesetmappings');
                    assert($nodesetmapping);
                    $nodesetmapping->nodeID = $nodeID;
                    $nodesetmapping->nodeSetID = $nodesetId;
                    $nodesetmappingId = $nodesetmapping->insert();
                }

                foreach ($json->edges as $edge) {
                    $edgeID = $this->add_edge($nodes_added['NA'.$edge->fromID], $nodes_added['NA'.$edge->toID]);
                }

                foreach ($json->schemefulfillments as $schemefulfillment) {
                    $sfins = $this->add_sf($nodes_added['NA'.$schemefulfillment->nodeID], $schemefulfillment->schemeID);
                }

                foreach ($json->participants as $participant) {
                    $participantID = $this->add_participant($participant->firstname, $participant->surname);
                    $p_added['PA'.$participant->participantID] = $participantID;
                }

                foreach ($json->locutions as $locution) {
                    $locutionID = $this->add_locution($nodes_added['NA'.$locution->nodeID], $p_added['PA'.$locution->personID]);
                }

                $return = "Imported as nodeSet " . $nodesetId;
                common_template('clean', '', $return);

            }else{
                common_user_error('Error uploading file');
            }

        }elseif($this->arg('nodesetid')){
            $nodeset_id = $this->arg('nodesetid');
            $JSON_nodes = array();
            if($nodeset_id == 'all'){
                $nodes = new Nodes;
                $nodes->find();
            }else{
                $nodeset = NodeSets::staticGet('nodeSetID', $nodeset_id);
                if (!$nodeset) {
                    $this->no_such_nodeset();
                    return;
                }
                $nodes = $nodeset->getNodes2();
                $nodes->find();
            }
            while($nodes->fetch()){
                if(strtolower($nodes->type) == 'i'){
                    $json = array();
                    $json['nodeID'] = $nodes->nodeID;
                    $json['text'] = $nodes->text;
                    $json['type'] = $nodes->type;
                    $json['timestamp'] = $nodes->timestamp;
                    if($nodes->name != NULL){
                        $json['scheme'] = $nodes->name;
                        $json['schemeID'] = $nodes->schemeID;
                    }
                    $JSON_nodes[] = json_encode($json);
                }
            }
            $node_output = implode(',', $JSON_nodes);
            $output = '{"nodes":[' . $node_output . ']}';

            common_template('clean', '', $output);
        }
    }

    function no_such_nodeset() {
        common_user_error('No such nodeset');
    }

    function add_node($text, $type) {
        $node_text = str_replace("\n", " ", trim($text));
        $existing_node = Nodes::staticGet('text', $node_text);

        if($existing_node && strtolower($type) != "ra" && strtolower($type) != "ca" && strtolower($type) != "ya" && strtolower($type) != "ta" && strtolower($type) != "ma" && strtolower($type) != "pa"){
            $return = $existing_node->nodeID;
        }else{
            $node = DB_DataObject::factory('nodes');
            assert($node);
            $node->text = $node_text;
            $node->type = $type;
            $return = $node->insert(); 
        }

        return $return; 
    }

    function add_edge($from, $to) {
        $edge = DB_DataObject::factory('edges');
        assert($edge);
        $edge->fromID = $from;
        $edge->toID = $to;

        if($edge->find()){
            $edge->fetch();
            $edgeId = $edge->{'edgeID'};
        }else{
            $edgeId = $edge->insert();  
        }

        return $edgeId;
    }


    function add_sf($nodeID, $schemeID) {
        $sf = DB_DataObject::factory('schemefulfillment');
        assert($sf);
        $sf->nodeID = $nodeID;
        $sf->schemeID = $schemeID;

        $ins = $sf->insert();

        return $ins;
    }

    function add_participant($firstname, $surname) {
        $personID = 6;
        $people = new People;
        $people->query("SELECT * FROM people WHERE firstName='" . $firstname . "' AND surname='" . $surname . "';");
        while($people->fetch()){
            $personID = $people->personID;
        }

        if($personID == 6){
            $prs = DB_DataObject::factory('people');
            assert($prs);
            $prs->firstName = $firstname;
            $prs->surname = $surname;
            $personID = $prs->insert();
        }

        return $personID;
    }

    function add_locution($nodeID, $personID) {
        $locution = DB_DataObject::factory('locutions');
        assert($locution);
        $locution->nodeID = $nodeID;
        $locution->personID = $personID;
        $locution->timestamp = date("Y-m-d H:i:s", time());

        $locution->insert();

        $return = "";
        $return .= $locution->encodeJSON();

        return $return;
    }
}
