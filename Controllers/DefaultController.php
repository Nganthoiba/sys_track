<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This is the default controller just for testing
 *
 * 
 */

class DefaultController extends Controller{
    public function index(){
        $this->data['content'] = 'Hello this is the index action of default controller.';
    }
    public function contact(){
        $this->data['content'] = 'Hello this is the contact action of default controller.';
    }
    public function about(){
        $this->data['content'] = 'Hello this is the about action of default controller.';
    }
    /*** just for testing ***/
    public function add(){
        $params = $this->getParams(); 
        $sum = 0;
        foreach ($params as $val){
            if(is_numeric($val)){
                $sum += $val;
            }
        }
        $this->send_data(array("Sum"=>$sum));
    }
    public function testing(){
        //return 6;
    }
    
}
