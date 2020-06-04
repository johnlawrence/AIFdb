<?php
/**
 * Table Definition for schemeFulfillment
 */
require_once 'DB/DataObject.php';

class SchemeFulfillment extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'schemeFulfillment';               // table name
    public $nodeID;                          // int(4)  primary_key not_null unsigned
    public $schemeID;                        // int(4)  primary_key not_null unsigned

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('SchemeFulfillment',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    public function encodeJSON(){
        $props = array("nodeID", "schemeID"); 
        foreach ($this as $key => $value){
            if(in_array($key, $props)){ 
                $json->$key = $value;
            }
        }
        return json_encode($json);
    }
}
