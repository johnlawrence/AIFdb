<?php
    $host = "ova.computing.dundee.ac.uk";
    $path = '/social/user/' . $_GET['name'];
    $user = 'arvina@dundee.ac.uk';
    $pass = '4rv1n4';

    $reply = "";

    $fp = fsockopen($host, 80, $err_num, $err_msg, 10);
    if (!$fp) {
        $reply = "$err_msg ($err_num)<br>\n";
    } else {
        $auth = base64_encode($user.":".$pass);
        fputs($fp, "GET $path HTTP/1.1\r\n");
        fputs($fp, "Authorization: Basic ".$auth."\r\n");
        fputs($fp, "Host: $host\n");
        fputs($fp, "Connection: close\n\n");
        $header = "";
        do{ 
            $header .= fgets($fp, 128);
            }while(strpos($header, "\r\n\r\n") === false && !feof($fp));

        $body = "";
            while(!feof($fp)){
            $body .= fgets ($fp, 128);
        }

        fclose($fp);

        $reply = $body;
    }

    echo $reply;
