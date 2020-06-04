<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <title><?php echo $template_title; ?></title>
        <link rel="meta" type="application/rdf+xml" title="AIFRDF" href="<?php echo common_path('rdf/'.$template_extra['nodeset']); ?>" />
        <link href="<?php echo common_path('css/argview.css'); ?>" media="screen" rel="stylesheet" type="text/css" />
        <script src="<?php echo common_path('extlib/argview/model.js'); ?>" type="text/javascript"></script>
        <script src="<?php echo common_path('extlib/argview/draw.js'); ?>" type="text/javascript"></script>
        <script src="<?php echo common_path('extlib/argview/svg-pan-zoom.js'); ?>" type="text/javascript"></script>
        <script src="<?php echo common_path('extlib/jquery/jquery-1.11.2.min.js'); ?>" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                $("div.odd").hover(function(){$(this).addClass("active");$(this).css('cursor','pointer');},
                                   function(){$(this).removeClass("active");$(this).css('cursor','auto');});
                $("div.even").hover(function(){$(this).addClass("active");$(this).css('cursor','pointer');},
                                    function(){$(this).removeClass("active");$(this).css('cursor','auto');});

                $('#dl #dll').click(function (e) {
                    $('#download').modal({containerId:'dl-container'});;
                    return false;
                });

                $('#dl #evl').click(function (e) {
                    $('#evaluate').modal({containerId:'eval-container'});
                    return false;
                });

                $('#dl #mml').click(function (e) {
                    $('#mainmenu').modal({containerId:'mm-container'});
                    return false;
                });

                $("#map").height($(window).height()-110);
            });

            function scrollTo(nx, ny){
                var _m = $("#map");
                var off = _m.offset();
                var dx = nx - off.left - _m.width() / 2;
                if(dx < 0){
                    dx = "+=" + dx + "px";
                }else{
                    dx = "-=" + -dx + "px";
                }
                var dy = ny - off.top - _m.height() / 2;
                if(dy < 0){
                    dy = "+=" + dy + "px";
                }else{
                    dy = "-=" + -dy + "px";
                }
                _m.animate({ scrollLeft:  dx, scrollTop: dy }, "normal", "swing");
            }

        </script>
    </head>

    <body>
        <div id="download">
            <a href="<?php echo common_local_url('diagram', array('nodesetid' => $template_extra['nodeset'], 'type' => 'svg')); ?>" 
               style="background-image:url('<?php echo common_path('images/svg_icon.png'); ?>');">Download SVG</a>
            <a href="<?php echo common_local_url('dot', array('nodesetid' => $template_extra['nodeset'])); ?>" 
               style="background-image:url('<?php echo common_path('images/dot_icon.png'); ?>');">Download DOT</a>
            <a href="<?php echo common_local_url('rdf', array('nodesetid' => $template_extra['nodeset'])); ?>" 
               style="background-image:url('<?php echo common_path('images/rdf_icon.png'); ?>');">Download RDF</a>
            <a href="<?php echo common_local_url('pl', array('nodesetid' => $template_extra['nodeset'])); ?>" 
               style="background-image:url('<?php echo common_path('images/pl_icon.png'); ?>');">Download Prolog</a>
            <a href="<?php echo common_local_url('rtnl', array('nodesetid' => $template_extra['nodeset'])); ?>" 
               style="background-image:url('<?php echo common_path('images/rtnl_icon.png'); ?>');">Download for Rationale (RTNL)</a>
            <a href="<?php echo common_local_url('lkif', array('nodesetid' => $template_extra['nodeset'])); ?>" 
               style="background-image:url('<?php echo common_path('images/lkif_icon.png'); ?>');">Download for Carneades (LKIF)</a>
        </div>
        <div id="evaluate">
            <a href="http://toast.arg.tech/aifdb/<?php echo $template_extra['nodeset']; ?>" 
               style="background-image:url('<?php echo common_path('images/toast_icon.png'); ?>'); target-name:new; target-new:tab;" target="_blank">Evaluate with Toast (ASPIC+)</a>
            <a href="http://tweety.arg-tech.org/<?php echo $template_extra['nodeset']; ?>" 
               style="background-image:url('<?php echo common_path('images/tweety_icon.png'); ?>'); target-name:new; target-new:tab;" target="_blank">Evaluate with Tweety (DeLP)</a>
            <a href="http://argsemsat.arg.tech/<?php echo $template_extra['nodeset']; ?>/grounded"
               style="background-image:url('<?php echo common_path('images/argsemsat_icon.png'); ?>'); target-name:new; target-new:tab;" target="_blank">Evaluate with ArgSemSAT</a>
        </div>
        <div id="mainmenu">
            <?php if($template_extra['plus']){ ?>
            <a href="<?php echo common_local_url('argview', array('nodesetid' => $template_extra['nodeset'])); ?>"
                style="background-image:url('<?php echo common_path('images/dialogue_icon.png'); ?>');">Hide dialogue view</a>
            <?php }else{ ?>
            <a href="<?php echo common_local_url('argview', array('nodesetid' => $template_extra['nodeset'], 'plus' => 'true')); ?>"
                style="background-image:url('<?php echo common_path('images/dialogue_icon.png'); ?>');">Show dialogue view</a>
            <?php } ?>
            <a href="http://www.arg.dundee.ac.uk/aif-corpora/?a=<?php echo $template_extra['nodeset']; ?>"
                style="background-image:url('<?php echo common_path('images/corpora_icon.png'); ?>');">View corpora</a>
            <a href="http://analytics.arg-tech.org/overview.php?n=<?php echo $template_extra['nodeset']; ?>"
                style="background-image:url('<?php echo common_path('images/analytics-icon.png'); ?>');">View analytics</a>
            <a href="http://ova.arg-tech.org/analyse.php?url=local&aifdb=<?php echo $template_extra['nodeset']; ?>"
                style="background-image:url('<?php echo common_path('images/ova-icon.png'); ?>');">Edit with OVA</a>
            <a href="http://ova.arg-tech.org/analyse.php?url=local&plus=true&aifdb=<?php echo $template_extra['nodeset']; ?>"
                style="background-image:url('<?php echo common_path('images/ovap-icon.png'); ?>');">Edit with OVA+</a>
        </div>
        <div id="header">
            <h1 style="margin-top: 28px;"><img src="<?php echo common_path('images/AIFdb_bw.png'); ?>" height="28px" alt="AIFdb" /> &nbsp; Argument Map <?php echo $template_extra['nodeset']; ?></h1> 
            <div id="dl">
                <a href="" 
                    style="background-image:url('<?php echo common_path('images/mmenu.png'); ?>');" id="mml">Menu</a>
                <a href="" 
                    style="background-image:url('<?php echo common_path('images/tick.png'); ?>');" id="evl">Evaluate</a>
                <a href="<?php echo common_local_url('svg', array('nodesetid' => $template_extra['nodeset'])); ?>" 
                    style="background-image:url('<?php echo common_path('images/dl.png'); ?>');" id="dll">Download</a>
            </div>
            <div id="tabs">
                <div id="details">Details</div>
                <div id="diagram">Diagram</div>
                <div id="ctz" style="display: none;">Double click to zoom in</div>
            </div>
        </div>
        <div id="argtext">
            <?php echo $template_body; ?>
        </div>
        <div id="vis">
        <div id="map">
            <svg xmlns="http://www.w3.org/2000/svg"
  version="1.1"
  style="width:1000px; height:1000px;"
  onmousedown='Grab(evt);'
  onload='Init1(evt, <?php echo $template_extra['nodeset']; ?>);'
  id='inline1'>
  <defs>
      <marker id='head3' orient="auto"
        markerWidth='12' markerHeight='10'
        refX='12' refY='5'>
        <!-- triangle pointing right (+x) -->
        <path d='M0,0 V10 L12,5 Z' fill="black"/>
      </marker>
  </defs>
</svg>
        </div>
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
