<!doctype html>
<html lang="en">
<head>
    <title>Merge NodeSets</title>
    <meta name="description" content="">
    <link rel="stylesheet" href="res/css/mergenodesets.css" />
    <script language="javascript" type="text/javascript" src="res/js/jquery-1.11.1.min.js"></script>
    <script language="javascript" type="text/javascript" src="res/js/mergenodesets.js"></script>
</head>

<body>
<div id="ccol">
    <img src="../images/AIFdb_small.png" />
    <div id="nsmform">
        <h2>Enter comma separated list of nodesets to merge</h2>
        <input type="text" name="nsm" id="nsm">
        <p style="text-align:right">
            <!--<button class="pure-button pure-button-primary" onClick="checkPeople();">Merge</button>-->
            <button class="pure-button pure-button-primary" onClick="doMerge();">Merge</button>
        </p>
    </div>
    <div id="progressbar" style="display:none">
        <h2>Merging nodesets...</h2>
        <p style="text-align:center; margin: 50px 10px;">
            <img src="res/img/loading.gif" />
        </p>
    </div>
    <div id="rescont" style="display:none">
        <h2>Nodesets successfully merged</h2>
        <p id="results">
        </p>
    </div>
    <div id="participantlist"></div>
</div>
</body>
</html>
