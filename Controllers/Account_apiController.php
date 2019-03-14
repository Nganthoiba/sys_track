<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Account_apiController
 * This is a controller that inherits REST Api
 * @author Nganthoiba
 */
class Account_apiController extends Api{
    
    public function __construct() {
        parent::__construct();
        $this->model = new Users();
    }

    //put your code here
    public function register(){
        try{     
            if($this->method == "POST"){
                $data = json_decode(file_get_contents("php://input"));
                $user = new Users();//$this->getModel();
                if($user->isEmailAlreadyExist($data->email)){
                    return $this->_response(array("message"=>"Email already exist, try with another.","status"=>false), 500);
                }
                if(isset($data->f_name) && isset($data->l_name) && isset($data->email) && isset($data->phone_no) )
                {
                    $user->f_name = $data->f_name;
                    $user->l_name = $data->l_name;
                    $user->email = $data->email;
                    $user->phone_no = $data->phone_no;
                    $user->password = $data->password;
                    $user->role_id = $data->role_id;
                    if($user->create()){
                        return $this->_response(array("message"=>"Registration successful"));
                    }
                    else{
                        return $this->_response(array("message"=>"Failed to register"), 500);
                    }
                }
                else{
                    return $this->_response(array("message"=>"Invalid parameters"), 500);
                }

            }
            else{
                return $this->_response(array("message"=>"Invalid request"), 404);
            }
        }catch(Exception $e){
            return $this->_response(array("message"=>"internal Server Error"), 500);
        }
    }
    
    public function login(){
        try{
            if($this->method == "POST"){
                $data = json_decode(file_get_contents("php://input"));
                $user = new Users();
                $email = htmlspecialchars(strip_tags($data->email));
                $password= htmlspecialchars(strip_tags($data->password));
                $res = $user->read(" WHERE email = '$email' AND password = '$password' ");
                
                if(count($res)==1){
                    $user=$res[0];
                    $id = $user['Id'];//getting user id
                    $token = $this->generateAuthToken($id);
                    if($token == null){
                        $response = array("message"=>"Failed to generate token.","user"=>$user);
                        return $this->_response($response,500);
                    }
                    $response = array("message"=>"You have successfully logged in.","user"=>$user,"token"=>$token);
                    return $this->_response($response);
                }
                else{
                    $response = array("message"=>"You have failed to log in, please try with correct credentials.");
                    return $this->_response($response,404);
                }
                
            }
        }catch(Exception $e){
            $response = array("message"=>"Sorry, we have some problems at our end. ","error"=>$e->getMessage());
            return $this->_response($response,500);
        }
    }
    /* generate authentication token and there by creating login details */
    private function generateAuthToken($user_id){
        $token = randId(50);
        $login_detail = new Login_details();
        $login_detail->token_id = $token;
        $login_detail->user_id = $user_id;
        if($login_detail->create()){
            return $login_detail->token_id;
        }
        else{
            return null;
        }
    }
    
    public function logout(){
        $data = json_decode(file_get_contents("php://input"));
        if(isset($data->token)){
            $login_det = new Login_details();
            $login_det->token_id = $data->token;
            $login_det->delete();
            $response = array("message"=>"You have successfully logged out.","state"=>true);
            return $this->_response($response);
        }
        else{
            $response = array("message"=>"Token not sent","state"=>false);
            return $this->_response($response,500);
        }
    }
    
    public function checkToken(){
        $data = json_decode(file_get_contents("php://input"));
        if(isset($data->token)){
            $login_det = new Login_details();
            //$login_det->token_id = $data->token;
            $login_det = $login_det->find($data->token);
            if($login_det!=null){
                $response = array("message"=>"You have login session.","state"=>true);
                return $this->_response($response);
            }
            else{
                $response = array("message"=>"Your token is invalid.","state"=>false);
                return $this->_response($response,500);
            }
        }
        else{
            $response = array("message"=>"Token not sent","state"=>false);
            return $this->_response($response,500);
        }
    }
    
    public function changePassword(){
        $data = json_decode(file_get_contents("php://input"));
        if(isset($data->token) && isset($data->old_password) && isset($data->new_password) && isset($data->user_id)){
            
            $data->token=htmlspecialchars(strip_tags($data->token));
            $data->old_password=htmlspecialchars(strip_tags($data->old_password));
            $data->new_password=htmlspecialchars(strip_tags($data->new_password));
            $data->user_id=htmlspecialchars(strip_tags($data->user_id));
            
            $user = new Users();
            $user->find($data->user_id);
            
            if($data->old_password!=$user->password){
                $response = array("message"=>"Your old password is wrong","state"=>false);
                return $this->_response($response,500);
            }
            $user->password = $data->new_password;
            $user->update();
            $response = array("message"=>"Your password has been changed","state"=>true);
            return $this->_response($response,200);
        }
        else{
            $response = array("message"=>"Invalid parameters","state"=>false);
            return $this->_response($response,500);
        }
    }
}
