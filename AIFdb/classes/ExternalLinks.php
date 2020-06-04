<?php
/**
 * Table Definition for externalLinks
 */
require_once 'DB/DataObject.php';

class ExternalLinks extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'externalLinks';                   // table name
    public $table;                           // varchar(32)  primary_key not_null
    public $rowID;                           // int(4)  primary_key not_null
    public $field;                           // varchar(32)  primary_key not_null
    public $URI;                             // int(4)   not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('ExternalLinks',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
