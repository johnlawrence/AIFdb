<?php

class AnswerAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $personID = $this->arg('personID');
            $post_data = file_get_contents('php://input');
            $json_data = json_decode(str_replace('""', '"$q"', $post_data));

            $nodes = array();
            foreach ($json_data->nodes as $n) {
                if(substr($n->text, 0, 1) === "$"){
                    $missing = $n->nodeID;
                }
                $nodes[$n->nodeID] = $n;
            }

            foreach ($json_data->edges as $e) {
                if($e->from == $missing){
                    if($nodes[$e->to]->type == "RA"){
                        foreach ($json_data->edges as $ei) {
                            if($ei->from == $nodes[$e->to]->nodeID){
                                $q = 'SELECT st5.nodeID, st5.text, st5.type, st5.timestamp from (SELECT st4.*, nodes.nodeID as SID from (SELECT nodes.* from nodes INNER JOIN (SELECT edges.* FROM nodes INNER JOIN (SELECT edges.toID FROM nodes INNER JOIN locutions ON ( locutions.nodeID = nodes.nodeID ) INNER JOIN edges ON (locutions.nodeID = edges.fromID) WHERE locutions.personID = "' . $personID . '") st2 ON nodes.nodeID = st2.toID INNER JOIN edges on nodes.nodeID = edges.fromID WHERE nodes.type = "YA") st3 ON st3.toID = nodes.nodeID) st4 INNER JOIN edges ON edges.fromID = st4.nodeID INNER JOIN nodes ON nodes.nodeID = edges.toID WHERE nodes.type = "RA") st5 INNER JOIN edges ON edges.fromID = st5.SID INNER JOIN nodes ON nodes.nodeID = edges.toID WHERE nodes.text = "' . $nodes[$ei->to]->text . '" GROUP BY nodeID ORDER BY nodeID ASC;';
                            }
                        }
                    }else if($nodes[$e->to]->type == "CA"){
                        foreach ($json_data->edges as $ei) {
                            if($ei->from == $nodes[$e->to]->nodeID){
                                $q = 'SELECT st5.nodeID, st5.text, st5.type, st5.timestamp from (SELECT st4.*, nodes.nodeID as SID from (SELECT nodes.* from nodes INNER JOIN (SELECT edges.* FROM nodes INNER JOIN (SELECT edges.toID FROM nodes INNER JOIN locutions ON ( locutions.nodeID = nodes.nodeID ) INNER JOIN edges ON (locutions.nodeID = edges.fromID) WHERE locutions.personID = "' . $personID . '") st2 ON nodes.nodeID = st2.toID INNER JOIN edges on nodes.nodeID = edges.fromID WHERE nodes.type = "YA") st3 ON st3.toID = nodes.nodeID) st4 INNER JOIN edges ON edges.fromID = st4.nodeID INNER JOIN nodes ON nodes.nodeID = edges.toID WHERE nodes.type = "CA") st5 INNER JOIN edges ON edges.fromID = st5.SID INNER JOIN nodes ON nodes.nodeID = edges.toID WHERE nodes.text = "' . $nodes[$ei->to]->text . '" GROUP BY nodeID ORDER BY nodeID ASC;';
                            }
                        }
                    }
                }
            }

            $nodes = new Nodes;
            $nodes->query($q);
            $return = '{"nodes":[';
            $JSON = array();
            while($nodes->fetch()){
                $JSON[] = $nodes->encodeJSON();
            }
            $return = $return . implode(',', $JSON);
            $return = $return . ']}';

            common_template('clean', 'return', $return);
        }
    }

    function no_such_node() {
        common_user_error('No such node.');
        exit;
    }
}
