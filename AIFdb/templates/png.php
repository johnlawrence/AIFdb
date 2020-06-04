<?php
$cache_root = $template_extra['nodeset'].'_'.md5($template_body);
$uploaddir = INSTALLDIR.'/tmp/';

$tmp = $uploaddir . $cache_root . ".dottmp";"";
$fh = fopen($tmp, 'w') or die("can't open file");
fwrite($fh, $template_body);
fclose($fh);

$cache_dot = $uploaddir . $cache_root . ".dot";
$cache_png = $uploaddir . $cache_root . ".png";

if(!file_exists($cache_png)){
    if(!file_exists($cache_dot)){
        exec("/usr/bin/dot -Tdot -o $cache_dot $tmp");
    }
    exec("/usr/bin/dot -Tpng -o $cache_png $cache_dot");
}

header("Content-type: image/png");
passthru("cat $cache_png");
