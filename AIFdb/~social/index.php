<?php

$url = "http://ova.computing.dundee.ac.uk:8080/SocialArg/api/";

$action = $_GET['action'];

//print_r($_SERVER);

if($action=="auth"){
	header("location:http://ova.computing.dundee.ac.uk:8080/SocialArg/apiconnect.jsp?uid=$_GET[uid]&platform=$_GET[platform]");
}

$u = $_SERVER['PHP_AUTH_USER'];
$p = $_SERVER['PHP_AUTH_PW'];

echo $input = trim(file_get_contents("php://input"));

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url . $action);
if($input!="")
	curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERPWD, "$u:$p");

$response = curl_exec($ch);

echo ($action=="platform") ? handlePlatforms($response) : $response;

function handlePlatforms($input){
	$json = json_decode($input);

	$currentURL = "http://ova.computing.dundee.ac.uk:8080/SocialArg/apiconnect.jsp";
	$replaceURL = "http://www.arg.dundee.ac.uk/AIFdb/social/auth";

	$newPlatforms = array();

	foreach($json->platforms as $platform){
		$platform->authUrl = str_replace($currentURL,$replaceURL,$platform->authUrl);
		$newPlatforms[] = $platform;
	}


	return stripslashes(json_encode($newPlatforms));
}

?>
