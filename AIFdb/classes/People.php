<?php
/**
 * Table Definition for people
 */
require_once 'DB/DataObject.php';

class People extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'people';                          // table name
    public $personID;                        // int(4)  primary_key not_null unsigned
    public $firstName;                       // varchar(64)   not_null
    public $surname;                         // varchar(32)   not_null
    public $description;                     // text   not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('People',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    public function encodeJSON(){
        $props = array("personID", "firstName", "surname", "description"); 
        foreach ($this as $key => $value){
            if(in_array($key, $props)){ 
                $json->$key = $value;
            }
        }
        return json_encode($json);
    }
}
