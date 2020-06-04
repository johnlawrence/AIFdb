<?php

class opinionAction extends Action {

    function handle($args) {
        parent::handle($args);

        $nodeID = $this->arg('nodeID');
        $personID = $this->arg('personID');
            
$squery = <<<EOT
SELECT lns.* FROM locutions INNER JOIN
    (SELECT nodes.* FROM nodes INNER JOIN
        (SELECT edges.* FROM edges INNER JOIN
            (SELECT nodes.nodeID FROM nodes INNER JOIN
                (SELECT edges.* FROM edges INNER JOIN
                    (SELECT DISTINCT nodes.nodeID FROM nodes INNER JOIN
                        (SELECT edges.* FROM edges inner join
                            (SELECT nodes.nodeID FROM nodes INNER JOIN edges ON ( edges.fromID = nodes.nodeID ) WHERE edges.toID = $nodeID AND nodes.type="RA")st
                        ON st.nodeID = edges.toID)ets
                    ON ets.fromID = nodes.nodeID WHERE nodes.type="I")nds
                ON nds.nodeID = edges.toID)es
            ON nodes.nodeID = es.fromID WHERE nodes.type="YA")yas
        ON edges.toID = yas.nodeID)yaes
    ON yaes.fromID = nodes.nodeID WHERE nodes.type="L")lns
ON locutions.nodeID = lns.nodeID WHERE locutions.personID=$personID;
EOT;

$aquery = <<<EOT
SELECT lns.* FROM locutions INNER JOIN
    (SELECT nodes.* FROM nodes INNER JOIN
        (SELECT edges.* FROM edges INNER JOIN
            (SELECT nodes.nodeID FROM nodes INNER JOIN
                (SELECT edges.* FROM edges INNER JOIN
                    (SELECT DISTINCT nodes.nodeID FROM nodes INNER JOIN
                        (SELECT edges.* FROM edges inner join
                            (SELECT nodes.nodeID FROM nodes INNER JOIN edges ON ( edges.fromID = nodes.nodeID ) WHERE edges.toID = $nodeID AND nodes.type="CA")st
                        ON st.nodeID = edges.toID)ets
                    ON ets.fromID = nodes.nodeID WHERE nodes.type="I")nds
                ON nds.nodeID = edges.toID)es
            ON nodes.nodeID = es.fromID WHERE nodes.type="YA")yas
        ON edges.toID = yas.nodeID)yaes
    ON yaes.fromID = nodes.nodeID WHERE nodes.type="L")lns
ON locutions.nodeID = lns.nodeID WHERE locutions.personID=$personID;
EOT;

	$nodes = new Nodes;
	$nodes->query($squery);
	$SUPPORTJSON = array();
	while($nodes->fetch()){
            $SUPPORTJSON[] = '{"nodeID":"'.$nodes->nodeID.'","text":"'.$nodes->text.'","timestamp":"'.$nodes->timestamp.'"}';
	}

        $nodes = new Nodes;
        $nodes->query($aquery);
        $ATTACKJSON = array();
        while($nodes->fetch()){
            $ATTACKJSON[] = '{"nodeID":"'.$nodes->nodeID.'","text":"'.$nodes->text.'","timestamp":"'.$nodes->timestamp.'"}';
        }

        $return = '{"support":[';
	$return = $return . implode(',', $SUPPORTJSON);
        $return = $return . '],';

        $return = $return . '"attack":[';
        $return = $return . implode(',', $ATTACKJSON);
        $return = $return . ']}';

        common_template('clean', $title, $return);
    }
}
