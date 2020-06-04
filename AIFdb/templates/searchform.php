<?php
    if(isset($_COOKIE['stype'])){
        $stype = $_COOKIE['stype'];
        $stype = 'n';
    }else{
        $stype = 'n';
    }
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <title><?php echo $template_title; ?></title>
        <link href="<?php echo common_path('css/search.css'); ?>" media="screen" rel="stylesheet" type="text/css" />

        <link href="<?php echo common_path('extlib/date-picker-v4/css/datepicker.css'); ?>" media="screen" rel="stylesheet" type="text/css" />
        <script language="javascript" type="text/javascript" src="<?php echo common_local_path('extlib/date-picker-v4/js/datepicker.packed.js'); ?>"></script>

        <script language="javascript" type="text/javascript">
        <!--
        function chbox(view) {
            if(view == 'a'){
                document.getElementById("asrchi").value = document.getElementById("bsrchi").value;
            }
            //document.getElementById('a-tab').className=''; 
            //document.getElementById('b-tab').className='';
            //document.getElementById(view + '-tab').className='sel';

            document.getElementById('a-box').className='hdn'; 
            document.getElementById('b-box').className='hdn';
            document.getElementById(view + '-box').className='sel';

            return true;
        }

        function ssearch() {
        <?php if($stype == 'n'){ ?>
            chbox('b');
        <?php }else{ ?>
            chbox('a');
        <?php } ?>
        }
        // -->
        </script> 
    </head>

   <body onLoad="ssearch()">
        <div id="topmenu">
            <a href="upload/" id="uploadlink"><span>B</span> Upload</a>
            <a href="http://corpora.aifdb.org/" id="corporalink"><span>C</span> Corpora</a>
            <a href="http://analytics.arg-tech.org/" id="analyticslink"><span>A</span> Analytics</a>
        </div>

        <div id="header">
            <h1><img src="<?php echo common_path('images/AIFdb.png'); ?>" alt="AIFdb" width="320px" /></h1>
        </div>

    <div id="ccol">

        <!--
        <div id="bar">
            <a href="#" id="b-tab" class="sel" onClick="chbox('b'); return false;">Basic Search</a>
            <a href="#" id="a-tab" onClick="chbox('a'); return false;">Argument Map Search</a>
        </div>
        -->

        <div id="search">
        <div id="b-box" class="sel">
            <form method="GET" action="<?php echo common_local_path('search'); ?>"> 
                <input type="text" name="q" class="searchinput" id="bsrchi" value="" autocomplete="off" tabindex="10" />
                <br/>
                <input type="submit" class="submitinput" value="Search" /> 
                <input type="button" onClick="chbox('a'); return false;" class="submitinput" value="Advanced Search" />
            </form>
        </div>
        <div id="a-box" class="hdn">
            <form method="GET" action="<?php echo common_local_url('search'); ?>">
                <div class="sectionrule"><div class="srcontent">Text</div></div>
                <input type="text" name="q" class="searchinput" value="" tabindex="10" id="asrchi" />
                <div class="sectionrule"><div class="srcontent">Speaker</div></div>
                <input type="text" name="p" class="searchinput" value="" tabindex="10" />
                <input type="hidden" name="am" value="1" />
                <div class="sectionrule"><div class="srcontent">Date Added</div></div>
                <table class="split-date-wrap" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                    <td><input type="text" class="w2em" id="date-1-dd" name="date-1-dd" value="" maxlength="2" />/<label for="date-1-dd">DD</label></td>
                    <td><input type="text" class="w2em" id="date-1-mm" name="date-1-mm" value="" maxlength="2" />/<label for="date-1-mm">MM</label></td>
                    <td class="lastTD"><input type="text" class="w4em split-date fill-grid statusformat-l-cc-sp-d-sp-F-sp-Y show-weeks no-today-button" id="date-1" name="date-1" value="" maxlength="4" /><label for="date-1">YYYY</label></td>
                    <td> &nbsp; - &nbsp; </td>
                    <td><input type="text" class="w2em" id="date-2-dd" name="date-2-dd" value="" maxlength="2" />/<label for="date-2-dd">DD</label></td>
                    <td><input type="text" class="w2em" id="date-2-mm" name="date-2-mm" value="" maxlength="2" />/<label for="date-2-mm">MM</label></td>
                    <td class="lastTD"><input type="text" class="w4em split-date fill-grid statusformat-l-cc-sp-d-sp-F-sp-Y show-weeks no-today-button" id="date-2" name="date-2" value="" maxlength="4" /><label for="date-2">YYYY</label></td>
                    </tr>
                </table>
                <div class="sectionrule"><div class="srcontent">Scheme</div></div>
                <select name="s" class="searchselect">
                    <option value="">Select a Scheme</option>
                    <?php
                        foreach($template_extra['schemes'] as $scheme){
                            echo '<option value="'.$scheme.'">' . $scheme . '</option>';
                        }
                    ?>
                </select>

                <br />
                <input type="submit" class="submitinput" value="Search" />
            </form>
        </div>
        </div>
    </div>
    <!--<div style="text-align: center; color: #aaa; margin-top: 60px;"><?php echo $template_body; ?></div>-->
    
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
