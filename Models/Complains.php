<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Complains
 *
 * @author Nganthoiba
 */
class Complains extends Model{
    public $Id;
    public $sys_id;
    public $problem_area;
    public $create_at;
    public $update_at;
    public $user_id;
    //put your code here
    public function __construct() {
        parent::__construct();
        $this->table_name="complain";
    }
    
    public function create(){
        $this->Id = randId(26);
        $this->create_at = time();
        $this->update_at = 0;
        if(trim($this->sys_id)==""){
            throw new Exception("Invalid input, missing system id");
        }
        if(trim($this->problem_area)==""){
            throw new Exception("Please specify problem area");
        }
        if(trim($this->user_id)==""){
            throw new Exception("Please specify the put this complain");
        }
        $this->problem_area = htmlspecialchars(strip_tags($this->problem_area));
        $this->sys_id = htmlspecialchars(strip_tags($this->sys_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        
        $qry = "insert into ".$this->table_name."(Id,sys_id,problem_area,create_at,update_at,user_id) values(:Id,:sys_id,:problem_area,:create_at,:update_at,:user_id)";
        try{
            $stmt = self::$conn->prepare($qry);
            $stmt->bindParam(':Id', $this->Id);
            $stmt->bindParam(':sys_id', $this->sys_id);
            $stmt->bindParam(':problem_area', $this->problem_area);
            $stmt->bindParam(':create_at', $this->create_at);
            $stmt->bindParam(':update_at', $this->update_at);
            $stmt->bindParam(':user_id', $this->user_id);
            $res=$stmt->execute();
            if($res){ return true;} else {return false;}
        } catch (Exception $e){
            throw $e;
        }
        return false;
    }
    
    public function read($cond = ""){
        $qry = "select * from ".$this->table_name." ".$cond;
        $res = self::$conn->query($qry);
        //$rows = $res->fetchall(PDO::FETCH_ASSOC);
        $rows = array();
        $user = new Users();
        $system = new Systems();
        while($row = $res->fetch(PDO::FETCH_ASSOC)){
            $row['create_at'] = (int)$row['create_at'];
            $row['update_at'] = (int)$row['update_at'];
            $user_det = $user->find($row['user_id']);
            if($user_det!=null){
                $row['complainer_name'] = $user_det['f_name']." ".$user_det['l_name'];
            }
            else{
                $row['complainer_name'] = "unknown";
            }
            $system_det = $system->find($row['sys_id']);
            if($system_det!=null){
                $row['lab_no'] = $system_det['lab_no'];
                $row['sys_no'] = $system_det['sys_no'];
            }
            else{
                $row['lab_no'] = "";
                $row['sys_no'] = "";
            }
            $rows[] = $row;
        }
        return $rows;
    }
    
    public function update(){
        $this->update_at = time();
        $this->problem_area = htmlspecialchars(strip_tags($this->problem_area));
        $this->sys_id = htmlspecialchars(strip_tags($this->sys_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->Id = htmlspecialchars(strip_tags($this->Id));
        
        $qry = "update ".$this->table_name." set sys_id = :sys_id,problem_area = :problem_area, update_at = :update_at where Id = :Id ";
        try{
            $stmt = self::$conn->prepare($qry);
            $stmt->bindParam(':sys_id',$this->sys_id);
            $stmt->bindParam(':problem_area',$this->problem_area);
            $stmt->bindParam(':update_at',$this->update_at);
            $stmt->bindParam(':Id',$this->Id);
            if($stmt->execute()){
                return true;
            }
        } catch (Exception $e){
            throw $e;
        }
        return false;
    }
    
    public function delete(){
        $qry = "delete from ".$this->table_name." where Id=?";
        $stmt = self::$conn->prepare($qry);
        $stmt->bindParam(1, $this->Id);
        return $stmt->execute();
    }
    
    public function find($id){
        $id = htmlspecialchars(strip_tags($id));
        $rows = $this->read(" where Id = '$id' ");
        if(count($rows)>0){
            $row = $rows[0];
            /*
             $this->Id = $row['Id'];
            $this->sys_id = $row['sys_id'];
            $this->problem_area = $row['problem_area'];
            $this->create_at = $row['create_at'];
            $this->update_at = $row['update_at'];
            $this->user_id = $row['user_id'];
            return $this;
            */
            return $row;
        }
        else{
            return null;
        }
    }

}
