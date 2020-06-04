<?php
/**
 * Table Definition for edges
 */
require_once 'DB/DataObject.php';

class Edges extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'edges';                           // table name
    public $edgeID;                          // int(4)  primary_key not_null unsigned
    public $fromID;                          // int(4)   not_null unsigned
    public $toID;                            // int(4)   not_null unsigned
    public $formEdgeID;                      // int(4)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Edges',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    public static function getInSet($ns) {
        $edges= new Edges;
        $sql = "SELECT DISTINCT edges.* " .
               "FROM edges " .
               "WHERE edges.fromID IN (SELECT nodeID from nodeSetMappings WHERE nodeSetID IN (" . $ns . ")) " .
               "AND edges.toID IN (SELECT nodeID from nodeSetMappings WHERE nodeSetID IN (" . $ns . "));";
        $edges->query($sql);
        return $edges;
    }

    public function encodeJSON(){
        $props = array("edgeID", "fromID", "toID", "formEdgeID"); 
        if (!isset($json)) $json = new stdClass();
        foreach ($this as $key => $value){
            if(in_array($key, $props)){ 
                $json->$key = $value;
            }
        }
        return json_encode($json);
    }
}
