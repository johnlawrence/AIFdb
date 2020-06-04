<?php
$cache_root = $template_extra['nodeset'].'_'.md5($template_body);
$uploaddir = INSTALLDIR.'/tmp/';

$tmp = $uploaddir . $cache_root . ".dottmp";"";
$fh = fopen($tmp, 'w') or die("can't open file");
fwrite($fh, $template_body);
fclose($fh);

$cache_dot = $uploaddir . $cache_root . ".dot";

if(!file_exists($cache_dot)){
    exec("/usr/bin/dot -Tdot -o $cache_dot $tmp");
}

passthru("cat $cache_dot");
