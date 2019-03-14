<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SystemController
 *
 * @author Nganthoiba
 */
class SystemController extends Api{
    //put your code here
    public function __construct() {
        parent::__construct();
        $this->model = new Systems();
    }
    public function index(){
        return $this->get();
    }
    
    /*** API to get data ***/
    public function get(){
        $params = $this->getParams();
        if(count($params)>0){
            $id = $params[0];
            $system = $this->model->find($id);
            if($system==null){
                $data = array("status"=>false,"message"=>"System not found");
                return $this->_response($data, 404);
            }
            $data = array("status"=>true,"message"=>"System found","system"=>$system);
            return $this->_response($data, 200);
        }
        else{
            $systems = $this->model->read("order by lab_no,sys_no");
            $data = array("status"=>true,"message"=>"List of systems","no_of_rec"=>count($systems),"systems"=>$systems);
            return $this->_response($data, 200);
        }
    }
    /**** Api to enter a new system ***/
    
    public function create(){
        $token = getAuthorizedToken();
        if($token == null || !Login_details::isValidToken($token)){
            $data = array("status"=>false,"message"=>"Please login and try again, you are using invalid token.");
            return $this->_response($data, 403);
        }
        if($this->method=="POST"){
            $input_data = json_decode(file_get_contents("php://input"));
            $this->model->sys_no = htmlspecialchars(strip_tags($input_data->sys_no));
            $this->model->sys_name = htmlspecialchars(strip_tags($input_data->sys_name));
            if(trim($input_data->ip)!="" && !isValidIP($input_data->ip)){
                $data = array("status"=>false,"message"=>"Ip is not valid.");
                return $this->_response($data, 403);
            }
            $this->model->ip = htmlspecialchars(strip_tags($input_data->ip));
            $this->model->brand = htmlspecialchars(strip_tags($input_data->brand));
            $this->model->sys_type = htmlspecialchars(strip_tags($input_data->sys_type));
            $this->model->model_no = htmlspecialchars(strip_tags($input_data->model_no));
            $this->model->cpu = htmlspecialchars(strip_tags($input_data->cpu));
            $this->model->ram_size = htmlspecialchars(strip_tags($input_data->ram_size));
            $this->model->hdd_size = htmlspecialchars(strip_tags($input_data->hdd_size));
            $this->model->lab_no = htmlspecialchars(strip_tags($input_data->lab_no));
            $this->model->created_by = Login_details::getUserId($token);
            /***** check for IP conflict *****/
            if($this->isIpConflict($this->model->ip)){
                $data = array("status"=>false,"message"=>"Conflict found, system ip ".$this->model->ip." already exists.");
                return $this->_response($data, 404);
            }
            if($this->conflictFound($this->model->sys_no, $this->model->lab_no)){
                $data = array("status"=>false,"message"=>"Conflict found, system number ".$this->model->sys_no." of lab number ".$this->model->lab_no." already exist.");
                return $this->_response($data, 404);
            }
            try{
                $this->model->create();
            } catch (Exception $ex){
                $data = array("status"=>false,"message"=>"An error occurs: ".$ex->getMessage());
                return $this->_response($data, 500);
            }
            $data = array("status"=>true,"message"=>"Record saved successfully.");
            return $this->_response($data, 200);
        }
        $data = array("status"=>false,"message"=>"Invalid request");
        return $this->_response($data, 403);
    }
    
    public function update(){
        $token = getAuthorizedToken();
        if($token == null || !Login_details::isValidToken($token)){
            $data = array("status"=>false,"message"=>"Please login and try again, you are using invalid token.");
            return $this->_response($data, 403);
        }
        if($this->method == "PUT"){
            $input_data = json_decode(file_get_contents("php://input"));
            $this->model->Id = htmlspecialchars(strip_tags($input_data->Id));
            $this->model->sys_no = htmlspecialchars(strip_tags($input_data->sys_no));
            $this->model->sys_name = htmlspecialchars(strip_tags($input_data->sys_name));
            $this->model->ip = htmlspecialchars(strip_tags($input_data->ip));
            $this->model->brand = htmlspecialchars(strip_tags($input_data->brand));
            $this->model->sys_type = htmlspecialchars(strip_tags($input_data->sys_type));
            $this->model->model_no = htmlspecialchars(strip_tags($input_data->model_no));
            $this->model->cpu = htmlspecialchars(strip_tags($input_data->cpu));
            $this->model->ram_size = htmlspecialchars(strip_tags($input_data->ram_size));
            $this->model->hdd_size = htmlspecialchars(strip_tags($input_data->hdd_size));
            $this->model->lab_no = htmlspecialchars(strip_tags($input_data->lab_no));
            if(!isValidIP($this->model->ip)){
                $data = array("status"=>false,"message"=>"System ip ".$this->model->ip." is not valid.");
                return $this->_response($data, 404);
            }
            if($this->isIpConflictForUpdate($this->model->ip)){
                $data = array("status"=>false,"message"=>"Conflict found, system ip ".$this->model->ip." already exists.");
                return $this->_response($data, 404);
            }
            if($this->conflictFoundForUpdate($this->model->sys_no, $this->model->lab_no)){
                $data = array("status"=>false,"message"=>"Conflict found, system number ".$this->model->sys_no." of lab number ".$this->model->lab_no." already exist.");
                return $this->_response($data, 404);
            }
            
            try{
                $this->model->update();
            } catch (Exception $ex){
                $data = array("status"=>false,"message"=>"An error occurs: ".$ex->getMessage());
                return $this->_response($data, 500);
            }
            $data = array("status"=>true,"message"=>"Record updated successfully.");
            return $this->_response($data, 200);
        }
        $data = array("status"=>false,"message"=>"Invalid request");
        return $this->_response($data, 403);
    }
    
    public function delete(){
        $token = getAuthorizedToken();
        if($token == null || !Login_details::isValidToken($token)){
            $data = array("status"=>false,"message"=>"Please login and try again, you are using invalid token.");
            return $this->_response($data, 403);
        }
        if($this->method == "DELETE"){
            $param = $this->getParams();
            if(count($param)){
                $id = $param[0];
                $this->model->Id = $id;
                if($this->model->delete()){
                    $data = array("status"=>true,"message"=>"Record deleted successfully.");
                    return $this->_response($data, 200);
                }
                else{
                    $data = array("status"=>false,"message"=>"Failed to delete.");
                    return $this->_response($data, 500);
                }
            }else{
                $data = array("status"=>false,"message"=>"Please send the Id.");
                return $this->_response($data, 500);
            }
            
        }
        $data = array("status"=>false,"message"=>"Invalid request");
        return $this->_response($data, 403);
    }
    /*mothod to check for any conflict with system number and lab no*/
    private function conflictFound($sys_no,$lab_no){
        $res = $this->model->read("where sys_no=".$sys_no." and lab_no=".$lab_no);
        return (count($res)>0);
    }
    private function conflictFoundForUpdate($sys_no,$lab_no){
        $res = $this->model->read("where sys_no=".$sys_no." and lab_no=".$lab_no." and Id!='".$this->model->Id."'");
        return (count($res)>0);
    }
    /*mothod to check for any conflict with ip number alloted to the systems*/
    private function isIpConflict($ip = ""){
        if(trim($ip)!=""){
            $res = $this->model->read("where ip='".$ip."' ");
            return (count($res)>0);
        }else{
            return false;
        }
        return true;
    }
    private function isIpConflictForUpdate($ip = ""){
       
        if(trim($ip)!=""){
            $res = $this->model->read("where ip='".$ip."' and Id!='".$this->model->Id."'");
            return (count($res)>0);
        }else{
            return false;
        }
        return true;
    }
    
}
