<?php
/**
 * Table Definition for descriptorFulfillment
 */
require_once 'DB/DataObject.php';

class DescriptorFulfillment extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'descriptorFulfillment';           // table name
    public $nodeID;                          // int(4)  primary_key not_null unsigned
    public $descriptorID;                    // int(4)  primary_key not_null unsigned

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DescriptorFulfillment',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    public function encodeJSON(){
        $props = array("nodeID", "descriptorID"); 
        foreach ($this as $key => $value){
            if(in_array($key, $props)){ 
                $json->$key = $value;
            }
        }
        return json_encode($json);
    }
}
