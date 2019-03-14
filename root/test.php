<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//$obj = array("name"=>"Nganthoiba","address"=>"Imphal");
//$obj = json_decode(json_encode($obj));
class person{
    public static $id;
    public $name;
    public $address;
    public $sex;
}
$obj = new person();

$obj->name = "Tomba";
$obj->address = "Meitram";
$obj->sex = "M";
$obj->id = 6;

$obj2 = new person();

$obj2->name = "Tomba";
$obj2->address = "Meitram";
$obj2->sex = "M";
$obj2->id = 8;

//print json_encode($obj);
print_r($obj);
print_r($obj2);

echo "<br/>ID:".person::$id;
$ip = "127.0.0.x1";

if (filter_var($ip, FILTER_VALIDATE_IP)) {
    echo("$ip is a valid IP address");
} else {
    echo("$ip is not a valid IP address");
}

function isValidIP($ip){
    return filter_var($ip, FILTER_VALIDATE_IP);
}