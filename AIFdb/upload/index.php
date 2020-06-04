<!DOCTYPE html> 
<html lang="en"> 
<head> 
<title></title>                  
<link rel="stylesheet" media="screen, projection" href="screen.css" /> 
</head> 
 
<body class="home blog"> 

<div id="c_box">
<h1>AIFdb File Import</h1>
<form enctype="multipart/form-data" action="ul.php" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
Select a file:
<br />
<input name="uploadedfile" type="file" />
<br />
<br />
File type:
<br />
<select name="ftype">
    <option value="lkif">Carneades (LKIF)</option>
    <option value="rtnl">Rationale (RTNL)</option>
    <option value="aml">Araucaria (AML)</option>
    <option value="json">JSON</option>
</select>
<br />
<br />
<input type="submit" value="Upload File" />
</form>
</div>

</body>
</html>
