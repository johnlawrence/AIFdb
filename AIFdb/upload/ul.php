<?php

$host = 'www.aifdb.org';
$db = '/';

$target_path = "tmp/";
$fname = basename($_FILES['uploadedfile']['name']);
$target_path = $target_path . $fname; 

if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
    if($_POST['ftype'] == "aml"){
        require_once('amlparse/AmlAif.php');
        $a = new AmlAif(file_get_contents($target_path),$host,$db,'test','pass');
        $new_id = $a->addToDatabase();
        echo "Imported as <a href='http://www.aifdb.org/argview/" . $new_id . "'>nodeset " . $new_id . "</a>";
    }else{
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
        curl_setopt($ch, CURLOPT_URL, "http://$host$db".$_POST['ftype']."/");
        curl_setopt($ch, CURLOPT_USERPWD,"test:pass"); 
        curl_setopt($ch, CURLOPT_POST, true);
        $post = array(
            "file"=>"@".$target_path,
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
        //$response = curl_exec($ch);
        if( ! $result = curl_exec($ch)) {
            trigger_error(curl_error($ch)); 
        } 
        curl_close($ch);
        echo $result;
    }
}else{
    echo "There was an error uploading the file, please try again!";
}
