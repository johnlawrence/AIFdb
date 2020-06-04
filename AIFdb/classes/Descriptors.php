<?php
/**
 * Table Definition for descriptors
 */
require_once 'DB/DataObject.php';

class Descriptors extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'descriptors';                     // table name
    public $descriptorID;                    // int(4)  primary_key not_null unsigned
    public $text;                            // varchar(250)   not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Descriptors',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    public function encodeJSON(){
        $props = array("descriptorID", "text"); 
        foreach ($this as $key => $value){
            if(in_array($key, $props)){ 
                $json->$key = $value;
            }
        }
        return json_encode($json);
    }
}
