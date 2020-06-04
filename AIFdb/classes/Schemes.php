<?php
/**
 * Table Definition for schemes
 */
require_once 'DB/DataObject.php';

class Schemes extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'schemes';                         // table name
    public $schemeID;                        // int(4)  primary_key not_null unsigned
    public $name;                            // varchar(128)   not_null
    public $schemeTypeID;                    // int(4)   not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Schemes',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    public function encodeJSON(){
        $props = array("schemeID", "name", "schemeTypeID"); 
        if (!isset($json)) $json = new stdClass();
        foreach ($this as $key => $value){
            if(in_array($key, $props)){ 
                $json->$key = $value;
            }
        }
        return json_encode($json);
    }
}
