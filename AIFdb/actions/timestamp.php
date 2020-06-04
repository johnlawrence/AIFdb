<?php

class TimestampAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $json = json_decode(file_get_contents("php://input"));
            //$json = json_decode($jsondata);

            $return = '';
            $return .= print_r($json, true);

            foreach ($json->timestamps as $ts) {
                $l = new Locutions;
                $l->start = $ts->timestamp;
                $l->whereAdd('nodeID='.$ts->nodeID);
                $l->update(DB_DATAOBJECT_WHEREADD_ONLY);
                $return .= $l->update();
            }
            common_template('clean', '', $return);
        }
    }
}
