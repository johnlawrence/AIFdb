<?php

class Action {

    var $args;

    function Action() {
    }

    function arg($key) {
        if (array_key_exists($key, $this->args)) {
            return $this->args[$key];
        } else {
            return NULL;
        }
    }

    function trimmed($key) {
        $arg = $this->arg($key);
        return (is_string($arg)) ? trim($arg) : $arg;
    }
    
    function handle($argarray) {
        $strip = get_magic_quotes_gpc();
        $this->args = array();
        foreach ($argarray as $k => $v) {
            $this->args[$k] = ($strip) ? stripslashes($v) : $v;
        }
    }
    
    function boolean($key, $def=false) {
        $arg = strtolower($this->trimmed($key));
        
        if (is_null($arg)) {
            return $def;
        } else if (in_array($arg, array('true', 'yes', '1'))) {
            return true;
        } else if (in_array($arg, array('false', 'no', '0'))) {
            return false;
        } else {
            return $def;
        }
    }
}
