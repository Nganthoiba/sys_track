<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TransactionController:
 * The main purpose of this api controller is to keep track of every transactions processed by user, just to know 
 * at which stage, a particular transaction is pending.
 *
 * @author Nganthoiba
 */
class TransactionController extends Api{
    public function __construct() {
        parent::__construct();
        $this->model = new Transactions();
    }
    
    public function create(){
        $token = getAuthorizedToken();
        if($token == null || !Login_details::isValidToken($token)){
            $data = array("status"=>false,"message"=>"Please login and try again, you are using invalid token.");
            return $this->_response($data, 403);
        }
        if($this->method!="POST"){
            $data = array("status"=>false,"message"=>"Invalid request.");
            return $this->_response($data, 403);
        }
        $input_data = json_decode(file_get_contents("php://input"));
        $valid = $this->validate($input_data);
        if($valid['status']==false){
            return $this->_response($valid, 403);
        }
        $user_id = Login_details::getUserId($token);
        $this->model->complain_id=$input_data->complain_id;
        $this->model->user_id=$user_id;
        $this->model->step_id=$input_data->step_id;
        try{
            $this->model->create();
        } catch (Exception $ex){
            $data = array("status"=>false,"message"=>"An error occurs: ".$ex->getMessage());
            return $this->_response($data, 500);
        }
        $data = array("status"=>true,"message"=>"Record saved successfully.");
        return $this->_response($data, 200);
    }
      
    
    /******* private methods *******/
    private function validate($input_data){
        if(!isset($input_data->complain_id) || trim($input_data->complain_id)==""){
            return array("status"=>false,"message"=>"missing complain ID");
        }
        if(!isset($input_data->step_id) || trim($input_data->step_id)==""){
            return array("status"=>false,"message"=>"missing work flow step ID");
        }
        return array("status"=>true,"message"=>"Validated");
    }
    
    
}
