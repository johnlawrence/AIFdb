<?php
/**
 * Table Definition for nodeSetMappings
 */
require_once 'DB/DataObject.php';

class NodeSetMappings extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'nodeSetMappings';                 // table name
    public $nodeID;                          // int(4)  primary_key not_null unsigned
    public $nodeSetID;                       // int(4)  primary_key not_null unsigned

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('NodeSetMappings',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    public function encodeJSON(){
        $props = array("nodeID", "nodeSetID");
        foreach ($this as $key => $value){
            if(in_array($key, $props)){
                $json->$key = $value;
            }
        }
        return json_encode($json);
    }

}
