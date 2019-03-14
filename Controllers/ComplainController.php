<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ComplainController
 *
 * @author Nganthoiba
 */
class ComplainController extends Api {
    //put your code here
    public function __construct() {
        parent::__construct();
        $this->model = new Complains();
    }
    public function index(){
        return $this->get();
    }
    
    /**** this will insert a new data ****/
    public function create(){
        if($this->method != "POST"){
            $data =  array("message"=>"Invalid request","status"=>false);
            return $this->_response($data, 404);
        }
        //getting input data and decoded in json
        $data = json_decode(file_get_contents("php://input"));
        $token = getAuthorizedToken();
        if($token==null || !Login_details::isValidToken($token)){
            $data =  array("message"=>"Please login and try again.","status"=>false);
            return $this->_response($data, 404);
        }
        $validate = $this->validateData($data);
        if($validate['status'] == false){
            return $this->_response($validate, 404);
        }
        else{
            $complain = $this->model;
            $complain->sys_id = $this->getSystemId($data->sys_no,$data->lab_no);
            $complain->user_id = Login_details::getUserId($token);//$data->user_id;
            $complain->problem_area = $data->problem_area;
            try{
                $complain->create();
                /**** setting work flow status ***/
                $transaction = new Transactions();
                $transaction->complain_id = $complain->Id;
                $transaction->step_id = 1;
                $transaction->user_id = $complain->user_id;
                if(!$transaction->create())
                {
                    $complain->delete();
                }
                /*************************************/
                return $this->_response(array("status"=>true,"message"=>"Submitted successfully"));
            } catch (Exception $e){
                $data =  array("message"=>"Error:".$e->getMessage(),"status"=>false);
                return $this->_response($data, 404);
            }
        }
    }
    /**** this will read data ***/
    public function get(){
        $params = $this->getParams();
        if(count($params)){
            $id = htmlspecialchars(strip_tags($params[0]));
            $complain=$this->model->find($id);
            if($complain == null){
                return $this->_response(array("status"=>false,"message"=>"We do not find this complain ".$id),404);
            }
            return $this->_response(array("status"=>true,"message"=>"Complain found","complain"=>$complain));
        }
        return $this->_response(array("status"=>true,
            "message"=>"complain list",
            "complains"=> $this->model->read("order by create_at desc")));       
    }
    /**** this api will update the complain request ***/
    public function put(){
        if($this->method!="PUT"){
            return $this->_response(array("status"=>false,"message"=>"Invalid request"), 404);
        }
        
        /*** Token verification ***/
        $token = getAuthorizedToken();
        if($token==null || !Login_details::isValidToken($token)){
            return $this->_response(array("status"=>false,"message"=>"Invalid request"), 404);
        }
        //getting input data and decoded in json
        $data = json_decode(file_get_contents("php://input"));
        $validate = $this->validateData($data);
        if($validate['status']==false){
            return $this->_response($validate, 404);
        }
        $this->model->Id = $data->Id;
        $this->model->user_id = $data->user_id;
        $this->model->sys_id = $data->sys_id;
        $this->model->problem_area = $data->problem_area;
        try{
            $res = $this->model->update();
            if($res){
                return $this->response_data(array("status"=>false,"message"=>"Successfully updated"), 500);
            }
            else{
                return $this->response_data(array("status"=>false,"message"=>"Unable to update"), 500);
            }
        } catch (Exception $ex) {
            return $this->response_data(array("status"=>false,"message"=>$ex->getMessage()), 500);
        }
    }
    
    public function delete(){
        if($this->method!="DELETE"){
            return $this->_response(array("status"=>false,"message"=>"Invalid request"), 403);
        }
        $params = $this->getParams();
        if(count($params)){
            $id = htmlspecialchars(strip_tags($params[0]));
            $this->model->Id = $id;
            $res = $this->model->delete();
            if($res) {
                return $this->_response(array("status"=>false,"message"=>"Successfully deleted"), 200);
            }
        }
        return $this->_response(array("status"=>false,"message"=>"Failed to delete"), 403);
    }

    /***** method to get task pending for a particular user ***/
    public function userPendingTask(){
        /*** Token verification ***/
        $token = getAuthorizedToken();
        if($token==null || !Login_details::isValidToken($token)){
            return $this->_response(array("status"=>false,"message"=>"Invalid request"), 404);
        }
        $user_id = Login_details::getUserId($token);
        $user = new Users();
        $user_role = $user->getUserRoleId($user_id);
        $max_step = 1; //by default
        switch($user_role){
            case 1:
            case 2: $max_step = 1;
                break;
            case 3: $max_step = 2;
                break;
            case 4: $max_step = 4;
                break;          
        }
        try{
            $m = new Model();
            $complains = array();
            $qry = " select C.Id,max(T.step_id) as last_step from complain C,transactions T ".
                   " where C.Id = T.complain_id ".
                   " group by C.Id order by C.create_at desc";
            $res=$m::$conn->query($qry);
            while($row = $res->fetch(PDO::FETCH_ASSOC)){
                /**** Condition to find pending task *****/
                // If last step is equal to max_step
                if((int)$row['last_step']=$max_step){
                    $complains[] = $this->model->find($row['Id']);
                }
            }
            $data =  array("message"=>"list of pending tasks","complains"=>$complains,"status"=>true);
            return $this->_response($data, 200);
        }catch(Exception $e){
            $data =  array("message"=>"Error:".$e->getMessage(),"status"=>false);
            return $this->_response($data, 404);
        }
    }
    
    
    /******* private methods ******/
    private function validateData($data){
        
        /*if(!isset($data->user_id)){
            return array("status"=>false,"message"=>"User Id is empty");
        }
        */
        if(!isset($data->lab_no) || $data->lab_no==""){
            return array("status"=>false,"message"=>"Lab no. is empty");
        }
        if(!isset($data->sys_no) || $data->sys_no==""){
            return array("status"=>false,"message"=>"System no. is empty");
        }
        if($this->getSystemId($data->sys_no, $data->lab_no)==null){
            return array("status"=>false,"message"=>"The system you are complaining does not exist. "
                . "Please enter lab no and system no correctly. ");
        }
        return array("status"=>true);
    }
    
    private function getSystemId($sys_no,$lab_no){
        $system = new Systems();
        $res = $system->read("where sys_no = $sys_no and lab_no = $lab_no");
        if(count($res)>0){
            return $res[0]['Id'];
        }
        else{
            return null;
        }
    }
    
}
