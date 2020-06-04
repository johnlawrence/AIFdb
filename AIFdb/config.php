<?php

$config['site']['name'] = 'AIFdb';
$config['site']['server'] = $_SERVER['SERVER_NAME'];

if ($_SERVER['SERVER_PORT'] != 80){
	$config['site']['server'] .= ":" . $_SERVER['SERVER_PORT'];
}
	
$config['site']['path'] = '';

$config['site']['fancy'] = true;

$db_driver = "mysql";
$user = getenv("MYSQL_USER");
$password = getenv("MYSQL_PASSWORD");
$host = getenv("MYSQL_HOST");
$database_name = "aifdb";

$config['db']['database'] = "$db_driver://$user:$password@$host/$database_name";
