<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of view
 *
 * @author Nganthoiba
 */
class View {
    protected $data;
    protected $path;
    
    protected static function getDefaultViewPath(){
        $router = App::getRouter();
        if(!$router){
            return false;
        }
        $controller = $router->getController();
        $template_name = $router->getMethodPrefix().$router->getAction().'.html';
        return VIEWS_PATH.DS.$controller.DS.$template_name;
    }

    public function __construct($data=array(),$path = null) {
        
        if(!$path || ($path==null) || trim($path)==""){
            $this->path = self::getDefaultViewPath();  
        }
        else{
            $this->path = $path;
        }
        if(!file_exists($this->path)){
            throw new Exception("Template file is not found in the path: ".$path);
        }
        $this->data = $data;
    }
    
    public function render(){
        ob_start();
        $data = $this->data;
        
        if(file_exists($this->path)){
            include($this->path);
        }
        $content = ob_get_clean();
        return $content;
    }
}
