<?php
/**
 * Table Definition for formEdgeTypes
 */
require_once 'DB/DataObject.php';

class FormEdgeTypes extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'formEdgeTypes';                   // table name
    public $formEdgeTypeID;                  // int(4)  primary_key not_null
    public $name;                            // varchar(64)   not_null
    public $direction;                       // enum(3)   not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('FormEdgeTypes',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    public function encodeJSON(){
        $props = array("formEdgeTypeID", "name", "direction");
        foreach ($this as $key => $value){
            if(in_array($key, $props)){
                $json->$key = $value;
            }
        }
        return json_encode($json);
    }

}
