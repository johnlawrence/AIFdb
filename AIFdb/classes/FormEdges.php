<?php
/**
 * Table Definition for formEdges
 */
require_once 'DB/DataObject.php';

class FormEdges extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'formEdges';                       // table name
    public $formEdgeID;                      // int(4)  primary_key not_null unsigned
    public $schemeID;                        // int(4)   not_null unsigned
    public $descriptorID;                    // int(4)   unsigned
    public $schemeTarget;                    // int(4)   unsigned
    public $formEdgeTypeID;                  // int(4)   not_null
    public $name;                            // varchar(128)   not_null
    public $description;                     // varchar(128)  
    public $CQ;                              // varchar(128)  
    public $Explicit;                        // tinyint(1)   not_null default_1

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('FormEdges',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    public function encodeJSON(){
        $props = array("formEdgeID", "schemeID", "descriptorID", "schemeTarget", "formEdgeTypeID", "name", "description", "CQ", "Explicit"); 
        foreach ($this as $key => $value){
            if(in_array($key, $props)){ 
                $json->$key = $value;
            }
        }
        return json_encode($json);
    }
}
