<?php
    if(isset($_GET['am'])){
        setcookie('stype', 'm', time() + (60 * 5), "/");
    }else{
        setcookie('stype', 'n', time() + (60 * 5), "/");
    }
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <title><?php echo $template_title; ?></title>
        <link href="<?php echo common_path('css/searchresultsam.css'); ?>" media="screen" rel="stylesheet" type="text/css" />

        <link href="<?php echo common_path('extlib/date-picker-v4/css/datepicker.css'); ?>" media="screen" rel="stylesheet" type="text/css" />
        <script language="javascript" type="text/javascript" src="<?php echo common_local_path('extlib/date-picker-v4/js/datepicker.packed.js'); ?>"></script>

	<script language="javascript" type="text/javascript">
	<!--
	function chbox(view) {
	    document.getElementById('a-tab').className=''; 
	    document.getElementById('b-tab').className='';
	    document.getElementById(view + '-tab').className='sel';

	    document.getElementById('a-box').className='hdn'; 
            document.getElementById('b-box').className='hdn';
            document.getElementById(view + '-box').className='sel';

	    return true;
	}
	// -->
	</script> 
    </head>

   <body>
        <div id="topmenu">
            <a href="upload/" id="uploadlink">Upload</a>
            <a href="http://corpora.aifdb.org/" id="corporalink">Corpora</a>
        </div>

        <div id="header">
            <h1>
                <a href="<?php echo common_path(''); ?>">
                <img src="<?php echo common_path('images/AIFdb_small.png'); ?>" alt="AIFdb" height="34px" style="border: 0px;" />
                </a>
            </h1>
            <div id="b-box" class="sel">
                <form method="GET" action="<?php echo common_local_path('search'); ?>">
                <input type="text" name="q" class="searchinput" value="<?php echo $template_extra['q']; ?>" tabindex="10" />
                <input type="submit" id="submitinput" value="Search" />
                </form>
            </div>

        </div>

	<div id="ccol">
            <?php echo $template_body; ?>
	</div>
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-57246736-1', 'auto');
      ga('send', 'pageview');

    </script>
    </body>
</html>
