<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserController
 *
 * @author Nganthoiba
 */
class UserController extends Api{
    //put your code here
    public function __construct() {
        parent::__construct();
        $this->model = new Users;
    }
    
    public function index(){
        return $this->get();
    }
    
    public function get(){
        $data = $this->model->read();
        if(count($data)){
            $resp = array("status"=>true,
                "message"=>"you have found the following list of users","users"=>$data);
            return $this->_response($resp);
        }else{
            $resp = array("status"=>false,"message"=>"No user found.");
            return $this->_response($resp,404);
        }
    }
}
