<?php

require_once ROOT.DS.'Config'.DS.'config.php';
require_once ROOT.DS.'libs'.DS.'special_functions.php';

function __autoload($classname){
    /* All the class files in the libs directory to be included */
    $lib_path = ROOT.DS.'libs'.DS.strtolower($classname).'.class.php';
    $controller_path = ROOT.DS.'Controllers'.DS. str_replace('controller','',strtolower($classname)).'Controller.php';
    $model_path = ROOT.DS.'Models'.DS.strtolower($classname).'.php';
    if(file_exists($lib_path)){
        require_once ($lib_path);
    }elseif(file_exists($controller_path)){
        require_once ($controller_path);
    }elseif(file_exists($model_path)){
        require_once ($model_path);
    }
    else{
        throw new Exception("Failed to include class : ".$classname);
    }
}

