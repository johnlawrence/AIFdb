<?php
/**
 * Table Definition for nodeSets
 */
require_once 'DB/DataObject.php';

class NodeSets extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'nodeSets';                        // table name
    public $nodeSetID;                       // int(4)  primary_key not_null unsigned
    public $title;                           // varchar(128)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('NodeSets',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function getNodes() {
        $nodes = new Nodes;
        $sql = "SELECT DISTINCT nodes.* " . 
               "FROM nodes " . 
               "INNER JOIN " .
               "nodeSetMappings ON nodeSetMappings.nodeID = nodes.nodeID " .
               "WHERE nodeSetMappings.nodeSetID =  '$this->nodeSetID';";
        $nodes->query($sql);
        return $nodes;
    }

    function getNodes2() {
        $nodes = new Nodes;
        $nodeSetMappings = new NodeSetMappings;
        $schemeFulfillment = new SchemeFulfillment;
        $schemes = new Schemes;
        $schemeFulfillment->joinAdd($schemes, 'LEFT');
        $nodes->joinAdd($schemeFulfillment, 'LEFT');

        $nodeSetMappings->nodeSetID = $this->nodeSetID;
        $nodes->joinAdd($nodeSetMappings);
        $nodes->orderBy('nodes.nodeID ASC');

        return $nodes;
    }

    function getEdges() {
        $edges= new Edges;
        $sql = "SELECT DISTINCT edges.* " .
               "FROM edges " .
               "WHERE edges.fromID IN (SELECT nodeID from nodeSetMappings WHERE nodeSetID='$this->nodeSetID') " . 
               "AND edges.toID IN (SELECT nodeID from nodeSetMappings WHERE nodeSetID='$this->nodeSetID');";
        $edges->query($sql);
        return $edges;
    }

    function getLocutions() {
        $locutions = new Locutions;
        $sql = "SELECT DISTINCT locutions.* " .
               "FROM locutions " .
               "WHERE locutions.nodeID IN (SELECT nodeID from nodeSetMappings WHERE nodeSetID='$this->nodeSetID') ;";
        $locutions->query($sql);
        return $locutions;
    }

    public function encodeJSON(){
        $props = array("nodeSetID", "title"); 
        foreach ($this as $key => $value){
            if(in_array($key, $props)){ 
                $json->$key = $value;
            }
        }
        return json_encode($json);
    }
}
