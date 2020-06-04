<?php
    $SQLFile = './db.sql';
    $ConfigFile = 'config.php';
    $DBini = 'dataobject.ini';

    if(isset($_POST['username'])){

        $user = $_POST['username'];
        $host = $_POST['hostname'];
        $pass = $_POST['password'];
        $dbnm = $_POST['database'];

        $srvr = $_POST['server'];
        $path = $_POST['path'];

        $apth = $_SERVER[DOCUMENT_ROOT] . '/' . $path . '/classes';
     

        $con = mysql_connect($host,$user,$pass);
        if ($con !== false){

            mysql_select_db ($dbnm);

            $f = fopen($SQLFile,"r");
            $sqlFile = fread($f,filesize($SQLFile));
            fclose($f);
            $sqlArray = explode(';',$sqlFile);

            foreach ($sqlArray as $stmt) {
                if (strlen($stmt)>3){
                    $result = mysql_query($stmt);
                    if (!$result){
                        $sqlErrorCode = mysql_errno();
                        $sqlErrorText = mysql_error();
                        $sqlStmt      = $stmt;
                        break;
                    }
                }
            }
        }

        if ($sqlErrorCode == 0){
            echo "<tr><td>Installation was finished succesfully!</td></tr>";
        } else {
            echo "<tr><td>An error occured during installation!</td></tr>";
            echo "<tr><td>Error code: $sqlErrorCode</td></tr>";
            echo "<tr><td>Error text: $sqlErrorText</td></tr>";
            echo "<tr><td>Statement:<br/> $sqlStmt</td></tr>";
        }

        $plh = array("ADBSERVER", "ADBPATH", "ADBUSER", "ADBPASS", "ADBDBHOST", "ADBDB", "ADBCPATH");
        $val = array($srvr, $path, $user, $pass, $host, $dbnm, $apth);

        $config = file_get_contents($ConfigFile.".xxx");
        $cf = fopen('../'.$ConfigFile, 'w');
        fwrite($cf, str_replace($plh, $val, $config));
        fclose($cf);

        $db_ini = file_get_contents($DBini.".xxx");
        $df = fopen('../'.$DBini, 'w');
        fwrite($df, str_replace($plh, $val, $db_ini));
        fclose($df);
    }
?>


<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" >
    <table width="50%">
        <tr>
            <td>Hostname:</td>
            <td><input name="hostname" type="text" value="localhost" /></td>
        </tr>
        <tr>
            <td>Database name:</td>
            <td><input name="database" type="text" value="foo" /></td>
        </tr>
        <tr>
            <td>Username:</td>
            <td><input name="username" type="text" /></td>
        </tr>
        <tr>
            <td>Password:</td>
            <td><input name="password" type="password" /></td>
        </tr>
        
        <tr><td colspan="2" style="border-bottom: 1px solid #ddd;"></tr>

        <tr>
            <td>Server:</td>
            <td><input name="server" type="text" value="<?php echo $_SERVER[HTTP_HOST]; ?>" /></td>
        </tr>
        <tr>
            <td>Path:</td>
            <td><input name="path" type="text" value="<?php echo preg_replace('/\/?(.*)\/install\/.*/', '$1', $_SERVER[REQUEST_URI]); ?>" /></td>
        </tr>
        <tr>
            <td align="center" colspan="2">
                <input type="submit" name="submitBtn" value="Install" />
            </td>
        </tr>
    </table>  
</form>

 
