<?php

include "AmlAif.php";

$user = "araucaria_client";
$password = "";
$host = "babbage.computing.dundee.ac.uk";
$db = "araucaria_v1_0";

$conn = mysql_connect($host, $user, $password);
mysql_select_db($db);

$result = mysql_query("SELECT aml FROM arguments WHERE id > 104 LIMIT 100;");

while(($row = mysql_fetch_row($result)) !=null){
	$aml = trim($row[0]);
	$a = new AmlAif($aml, 'www.power-web.co.uk', '/AIF2DB2/', 'test', 'pass');
	echo $a->addToDatabase() . "\n";
}


?>
