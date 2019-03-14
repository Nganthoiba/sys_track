<?php

function generateRandomString($length = 32) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/*php function to generate random unique id*/
function randId($length){
    $id = md5(uniqid().time().generateRandomString());
    $char = str_shuffle($id);
    for($i = 0, $rand = '', $l = strlen($char) - 1; $i < $length; $i++) {
        $rand .= $char{mt_rand(0, $l)};
    }
    return $rand;
}

function filter($key,$method){
    if(trim($method)=="POST"){
        $value = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
    }else{
        $value = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
    }
    //$value = filter_input(INPUT_GET, $key, FILTER_SANITIZE_ENCODED);
    return trim($value);
}

function isLinkActive($link){
    $current_link = filter("uri", "GET");
    if(trim($link, '/') == trim($current_link,'/')){
        return "active";
    }
    else{
        return "";
    }
}

/*function to start session*/
function secure_session_start() {
    if (session_status() === PHP_SESSION_NONE){
        
        ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies. 
        ini_set("session.cookie_httponly", 1);//This will prevent from javascript to display session cookies over browser
        
        session_start(); // Start the php session
        session_regenerate_id(true); // regenerated the session, delete the old one.   
    }
}
/*function to get client ip*/
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP')){
        $ipaddress = getenv('HTTP_CLIENT_IP');
    }
    else if(getenv('HTTP_X_FORWARDED_FOR')){
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    }
    else if(getenv('HTTP_X_FORWARDED')){
    $ipaddress = getenv('HTTP_X_FORWARDED');}
    else if(getenv('HTTP_FORWARDED_FOR'))
    {$ipaddress = getenv('HTTP_FORWARDED_FOR');}
    else if(getenv('HTTP_FORWARDED'))
    {$ipaddress = getenv('HTTP_FORWARDED');}
    else if(getenv('REMOTE_ADDR'))
    {$ipaddress = getenv('REMOTE_ADDR');}
    else
    {$ipaddress = 'UNKNOWN';}
    return $ipaddress;
}

function getAuthorizedToken(){
    //function to get authorized token from http headers
    $headers = apache_request_headers();
    $token = get_data_from_array("Authorization",$headers);
    return $token;
}
function get_data_from_array($key_data,$array){
    foreach ($array as $key=>$value){
        if($key==$key_data){
            return $value;
        }
    }
    return null;
}
/*function to check for valid IP address*/
function isValidIP($ip){
    return filter_var($ip, FILTER_VALIDATE_IP);
}

