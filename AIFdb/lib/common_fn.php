<?php

function common_redirect($url, $code=307) {
    static $status = array(301 => "Moved Permanently",
                           302 => "Found",
                           303 => "See Other",
                           307 => "Temporary Redirect");

    header("Status: ${code} $status[$code]");
    header("Location: $url");
}

function common_local_url($action, $args=NULL) {
    global $config;
    if ($config['site']['fancy']) {
        return common_fancy_url($action, $args);
    } else {
        return common_simple_url($action, $args);
    }
}

function common_fancy_url($action, $args=NULL) {
    switch (strtolower($action)) {
        case 'search':
            return common_path('search');
        case 'aif':
            return common_path('aif/'.$args['nodesetid']);
        case 'rdf':
            return common_path('rdf/'.$args['nodesetid']);
        case 'dot':
            return common_path('dot/'.$args['nodesetid']);
        case 'pl':
            return common_path('pl/'.$args['nodesetid']);
        case 'json':
            return common_path('json/'.$args['nodesetid']);
        case 'diagram':
            if(isset($args['type'])){
                return common_path('diagram/'.$args['type'].'/'.$args['nodesetid']);
            }else{
                return common_path('diagram/'.$args['nodesetid']);
            }
        case 'argview':
            if(isset($args['plus'])){
                return common_path('argview/'.$args['nodesetid'].'?plus=on');
            }else{
                return common_path('argview/'.$args['nodesetid']);
            }
        case 'nodeview':
            return common_path('nodeview/'.$args['node']);
        case 'rtnl':
            return common_path('rtnl/'.$args['nodesetid']);
        case 'lkif':
            return common_path('lkif/'.$args['nodesetid']);
        default:
            return common_simple_url($action, $args);
    }
}

function common_simple_url($action, $args=NULL) {
    $extra = '';
    if ($args) {
        foreach ($args as $key => $value) {
            $extra .= "&${key}=${value}";
        }
    }
    return common_path("index.php?action=${action}${extra}");
}

function common_path($relative) {
    global $config;
    $pathpart = ($config['site']['path']) ? $config['site']['path']."/" : '';
    return "http://".$config['site']['server'].'/'.$pathpart.$relative;
}

function common_local_path($relative) {
    global $config;
    $pathpart = ($config['site']['path']) ? $config['site']['path']."/" : '';
    return '/'.$pathpart.$relative;
}

function common_template($template, $title, $body, $extra=NULL){
    $template_title = $title;
    $template_body = $body;
    $template_extra = $extra;
    require_once(INSTALLDIR.'/templates/'.$template.'.php');
}

function common_user_error($msg, $code=400) {
    common_template('clean', 'Error', $msg);
    exit(1);
}

function common_error($msg, $code=400) {
    common_template('clean', 'Error', $msg);
}

function common_input($id, $label, $type, $value=NULL) {
    $input = "";
    $value = htmlspecialchars($value);

    $input .= "<p>";
    $input .= "<label for='$id'>";
    $input .= $label;
    $input .= "</label>";
    $input .= "<input type='$type' name='$id' id='$id' value='$value' />";
    $input .= "</p>";

    return $input;
}

function common_string_check($string, $length) {
    if(!is_string($string)){
        return FALSE;
    }elseif(empty($string)){
        return FALSE;
    }elseif(strlen($string) > $length){
        return FALSE;
    }else{
        return TRUE;
    }
}

function common_password_gen($id, $password) {
    return md5($id . $password);
}

function common_check_user($username, $password) {
    $user = User::staticGet('username', $username);
    if (is_null($user)) {
        return false;
    } else {
        return (0 == strcmp(common_password_gen($username, $password), $user->password));
    }
}

function common_auth() {
    if(!isset($_SERVER[PHP_AUTH_USER]) || !isset($_SERVER[PHP_AUTH_PW])){
        common_user_error('Authentication error: No username or password');
	exit;
    }
    if(common_check_user($_SERVER[PHP_AUTH_USER], $_SERVER[PHP_AUTH_PW])){

    }else{
        common_user_error('Authentication error: Invalid username or password');
        exit;
    }    
}
