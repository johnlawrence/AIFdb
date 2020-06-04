<?php
/**
 * Table Definition for locutions
 */
require_once 'DB/DataObject.php';

class Locutions extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'locutions';                       // table name
    public $nodeID;                          // int(4)   not_null unsigned
    public $personID;                        // int(4)   not_null unsigned
    public $timestamp;                       // timestamp   not_null default_CURRENT_TIMESTAMP
    public $start;                           // varchar(64)  
    public $end;                             // varchar(64)  
    public $source;                          // varchar(256)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Locutions',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    public function encodeJSON(){
        $props = array("nodeID", "personID", "timestamp", "start", "end", "source"); 
        if (!isset($json)) $json = new stdClass();
        foreach ($this as $key => $value){
            if(in_array($key, $props)){ 
                $json->$key = $value;
            }
        }
        return json_encode($json);
    }


    public static function getInSet($ns) {
        $locutions = new Locutions;
        $sql = "SELECT DISTINCT locutions.* " .
               "FROM locutions " .
               "WHERE locutions.nodeID IN (SELECT nodeID from nodeSetMappings WHERE nodeSetID IN (" . $ns . "));";
        $locutions->query($sql);
        return $locutions;
    }
}
