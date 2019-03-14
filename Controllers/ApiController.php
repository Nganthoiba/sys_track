<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiController
 * Testing Rest Api controller
 * @author Nganthoiba
 */

class ApiController extends Api{
    public function __construct() {
        parent::__construct();
    }
    function test(){
        return $this->_response(array("message"=>"Hello World, this is an example of REST api"));
    }
}
