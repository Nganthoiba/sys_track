<?php

define('DS', '/');
define('ROOT', '../');
define('VIEWS_PATH', ROOT.DS.'Views');
require_once ROOT.DS.'libs/init.php';

//$uri = trim($_SERVER['REQUEST_URI'], '/');
$uri = filter('uri', "GET");
try{
    App::run($uri);
}
catch(Exception $e){
    echo 'Error :'.$e->getMessage();
}
