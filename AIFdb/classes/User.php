<?php
/**
 * Table Definition for user
 */
require_once 'DB/DataObject.php';

class User extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'user';                            // table name
    public $userID;                          // int(4)  primary_key not_null
    public $username;                        // varchar(64)  unique_key
    public $password;                        // varchar(255)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('User',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
