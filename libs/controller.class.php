<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of controller
 *
 * @author Nganthoiba
 */
class Controller {
    protected $params;
    protected $model;
    protected $data;
    
    public function getData(){
        return $this->data;
    }
    public function getModel(){
        return $this->model;
    }
    public function getParams(){
        return $this->params;
    }
    public function __construct($data = array()) {
        $this->data = $data;
        $this->params = App::getRouter()->getParams();
    }
    
    public function send_data($data = array()){
        header("Content-Type: application/json");
        echo json_encode($data);
        exit();
    }
}
