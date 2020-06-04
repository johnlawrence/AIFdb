<?php
/**
 * Table Definition for nodes
 */
require_once 'DB/DataObject.php';

class Nodes extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'nodes';                           // table name
    public $nodeID;                          // int(4)  primary_key not_null unsigned
    public $text;                            // longtext   not_null
    public $type;                            // varchar(2)   not_null
    public $timestamp;                       // timestamp   not_null default_CURRENT_TIMESTAMP

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Nodes',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function getNodesIn() {
        $nodes = new Nodes;
        $nodes->query("SELECT * FROM nodes INNER JOIN edges ON edges.fromID=nodes.nodeID WHERE edges.toID=" . $this->nodeID . " ORDER BY nodeID");
        return $nodes;
    }

    public static function getInSet($ns) {
        $nodes = new Nodes;
        $nsl = str_replace(',', '),(', $ns);
        $sql = "create temporary table nsids (nsID int); INSERT INTO nsids (nsID) VALUES (" . $nsl . "); SELECT DISTINCT nodes.* FROM nodes INNER JOIN nodeSetMappings ON nodeSetMappings.nodeID = nodes.nodeID INNER JOIN nsids ON nsids.nsID = nodeSetMappings.nodeSetID;";
        $sql = "SELECT DISTINCT nodes.* " .
               "FROM nodes " .
               "INNER JOIN " .
               "nodeSetMappings ON nodeSetMappings.nodeID = nodes.nodeID " .
               "WHERE nodeSetMappings.nodeSetID IN (" . $ns . ");";
        $nodes->query($sql);
        return $nodes;
    }

    function getNodesOut2() {
        $nodes = new Nodes;
        $edges = new Edges;
        $edges->toID = $this->nodeID;
        $nodes->joinAdd($edges);
        $nodes->orderBy('nodeID ASC');

        return $nodes;
    }

    function getNodesOut() {
        $nodes = new Nodes;
        $nodes->query("SELECT * FROM nodes INNER JOIN edges ON edges.toID=nodes.nodeID WHERE edges.fromID=" . $this->nodeID . " ORDER BY nodeID");
        return $nodes;
    }

    function getNodesOutInSet($nodeSetID) {
        $nodes = new Nodes;
        $nodes->query("SELECT * FROM nodes INNER JOIN nodeSetMappings ON nodeSetMappings.nodeID=nodes.nodeID INNER JOIN edges ON edges.toID=nodes.nodeID WHERE edges.fromID=" . $this->nodeID . " AND nodeSetMappings.nodeSetID=" . $nodeSetID . " ORDER BY nodes.nodeID");
        return $nodes;
    }

    function getNodesInInSet($nodeSetID) {
        $nodes = new Nodes;
        $nodes->query("SELECT * FROM nodes INNER JOIN nodeSetMappings ON nodeSetMappings.nodeID=nodes.nodeID INNER JOIN edges ON edges.fromID=nodes.nodeID WHERE edges.toID=" . $this->nodeID . " AND nodeSetMappings.nodeSetID=" . $nodeSetID . " ORDER BY nodes.nodeID");
        return $nodes;
    }

    function getNodesIn2() {
        $nodes = new Nodes;
        $edges = new Edges;
        $edges->fromID = $this->nodeID;
        $nodes->joinAdd($edges);
        $nodes->orderBy('nodeID ASC');

        return $nodes;
    }

    public function getScheme() {
        $schemes = new Schemes;
        $schemes->query("SELECT * FROM schemes INNER JOIN schemeFulfillment ON schemes.schemeID=schemeFulfillment.schemeID WHERE schemeFulfillment.nodeID=".$this->nodeID);

        $s = "";
        while($schemes->fetch()){
            $s = $schemes->name;
        }

        if($s == ""){
            if($this->type == "YA"){
                $s = "Default Illocuting";
            }elseif($this->type == "TA"){
                $s = "Default Transition";
            }
        }

        return $s;
    }

    public function encodeJSON(){
        $props = array("nodeID", "text", "type", "timestamp"); 
        if (!isset($json)) $json = new stdClass();
        foreach ($this as $key => $value){
            if(in_array($key, $props)){ 
                $json->$key = $value;
            }
        }
        return json_encode($json);
    }
}
