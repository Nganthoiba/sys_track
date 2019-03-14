<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AccountController
 *
 * @author Nganthoiba
 */
class AccountController extends Controller{
    public function __construct($data = array()) {
        parent::__construct($data);
        $this->model = new Users();
    }

    public function login(){
        
    }
    public function register(){
        
    }
    
    
}
