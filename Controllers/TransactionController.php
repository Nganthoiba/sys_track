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
            /*
            if(isset($input_data->message) && trim($input_data->message)!=""){
                $post = new Posts();
                $post->complain_id = $input_data->complain_id;
                $post->user_id = $user_id;
                $post->message = $input_data->message;
                $post->create();
            }
            */
        } catch (Exception $ex){
            $data = array("status"=>false,"message"=>"An error occurs: ".$ex->getMessage());
            return $this->_response($data, 500);
        }
        $data = array("status"=>true,"message"=>"Record saved successfully.");
        return $this->_response($data, 200);
    }
    
    public function index(){
        return $this->get();
    }
    public function get(){
        $data = array("status"=>true,"message"=>"All transactions","transactions"=> $this->model->read());
        return $this->_response($data, 200);
    }
    
    /*** Only for Technical Officer ***/
    public function postByTO(){
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
        $user_id = Login_details::getUserId($token);
        if(!isset($input_data->complain_id) || trim($input_data->complain_id)==""){
            $data = array("status"=>false,"message"=>"Invalid request.");
            return $this->_response($data, 403);
        }
        
        $model = new Model();
        $conn = $model::$conn;
        $conn->beginTransaction();//beginning transaction
        $sys_status = $input_data->problem_fixed==true?'Y':'N';
        /**** setting whether proble fixed or not ****/
        $qry1 = "update complain set problem_fixed = '$sys_status' where Id='".$input_data->complain_id."'";
        $res1 = $conn->query($qry1);
        if(!$res1){
            $data = array("status"=>false,"message"=>"Unable to save data.");
            return $this->_response($data, 500);
        }
        
        /***** adding a new record in transaction history if the complain is to be forwarded to Reporting Officer ****/

        if(isset($input_data->forward_to_ro) && $input_data->forward_to_ro==true){
            $txn = new Transactions();
            $txn->step_id=2;
            $txn->complain_id = $input_data->complain_id;
            $txn->user_id = $user_id;
            if(!$txn->create()){
                $conn->rollback();
                $data = array("status"=>false,"message"=>"An error occurs.");
                return $this->_response($data, 500);
            }
        }
        $conn->commit();
        
        $data = array("status"=>true,"message"=>"Submitted successfully.");
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
