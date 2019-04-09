<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PostController
 * This api is about to save, update, or delete a message
 *
 * @author Nganthoiba
 */
class PostController extends Api{
    public function __construct() {
        parent::__construct();
        $this->model = new Posts();
    }
    public function index(){
        return $this->get();
    }
    public function get(){
        $data = array("status"=>true,
            "message"=>"List of messages posted by different users",
            "posts"=> $this->model->read(" order by create_at desc "));
        return $this->_response($data,200);
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
        $user_id = Login_details::getUserId($token);
        /*** check if there is message ***/
        if(isset($input_data->message) && trim($input_data->message)!=""){    
            $this->model->user_id = $user_id;
            $this->model->message = $input_data->message;
            if($this->model->create()){
                $data = array("status"=>true,"message"=>"Message sent successfully");
                return $this->_response($data);
            }
        }
        $data = array("status"=>false,"message"=>"Failed to send your message.");
        return $this->_response($data,403);
    }
    
    public function delete(){
        $param = $this->getParams();
        if(count($param)==0){
            $data = array("status"=>false,"message"=>"Invalid request");
            return $this->_response($data,403); 
        }
        if($this->method!="DELETE"){
            $data = array("status"=>false,"message"=>"Invalid request.");
            return $this->_response($data, 403);
        }
        $message_id = $param[0];
        if($this->model->delete($message_id)){
            $data = array("status"=>true,"message"=>"Message deleted successfully.");
            return $this->_response($data, 200);
        }
        else{
            $data = array("status"=>false,"message"=>"Failed to delete your message.");
            return $this->_response($data, 403);
        }
    }
}
