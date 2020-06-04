<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <title><?php echo $template_title; ?></title>
        <link href="<?php echo common_path('css/nodeview.css'); ?>" media="screen" rel="stylesheet" type="text/css" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js"></script>
        <script src="<?php echo common_path('extlib/nodeview/arbor.js'); ?>"></script>
        <script src="<?php echo common_path('extlib/nodeview/arbor-graphics.js'); ?>"></script>
        <script type="text/javascript">
            var nodeID = <?php echo $template_extra['node'] ?>;
            var dbURL='<?php echo common_path(''); ?>';
        </script>
    </head>

    <body>
        <div id="header">
            <h1><img src="<?php echo common_path('images/AIFdb_bw.png'); ?>" height="28px" alt="AIFdb" /></h1> 
            <div id="tabs">
                <div id="details">Details</div>
                <div id="diagram">Diagram</div>
            </div>
        </div>
        <div id="argtext">
            <?php echo $template_body; ?>
        </div>
        <div id="vis">
        <div id="map">
        <div id="wmap">
            <canvas id="argcanvas" width="800" height="600"></canvas> 
        </div>
        </div>
        </div>
        <script src="<?php echo common_path('extlib/nodeview/main.js'); ?>"></script>
    </body>
</html>
