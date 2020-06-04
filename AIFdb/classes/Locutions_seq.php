<?php
/**
 * Table Definition for locutions_seq
 */
require_once 'DB/DataObject.php';

class Locutions_seq extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'locutions_seq';                   // table name
    public $sequence;                        // int(4)  primary_key not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Locutions_seq',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
