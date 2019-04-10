<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Users
 *
 * @author Nganthoiba
 */
class Users extends Model{
    
    public $Id;
    public $f_name;
    public $l_name;
    public $email;
    public $phone_no;
    public $role_id;
    public $password;
    public $create_at;
    
    //put your code here
    public function __construct() {
        parent::__construct();
        $this->table_name = "users";
    }
    
    public function find($id){
        //$user = new Users();
        //$res = $this->read("where Id = '".$id."'");
        $qry = "select * from ".$this->table_name." where Id = '$id'";
        $res = self::$conn->query($qry);
        $rows = $res->fetchall(PDO::FETCH_ASSOC);
        if(count($rows)>0){
            $row = $rows[0];
            $this->Id = $id;
            $this->f_name = $row['f_name'];
            $this->l_name = $row['l_name'];
            $this->email = $row['email'];
            $this->role_id = $row['role_id'];
            $this->phone_no = $row['phone_no'];
            $this->password = $row['password'];
            $this->create_at = $row['create_at'];
            return $row;
        }
        return null;
    }
    
    public function read($cond=""){
        $qry = "select Id,f_name,l_name,email,phone_no,role_id,create_at from ".$this->table_name." ".$cond;
        $res = self::$conn->query($qry);
        $data = array();
        $rows = $res->fetchall(PDO::FETCH_ASSOC);
        /*while ($row = $res->fetch(PDO::FETCH_ASSOC)){
            $row['role_id'] = (int)$row['role_id'];
            $row['create_at'] = (int)$row['create_at'];
            $rows[] = $row;
        }*/
        foreach ($rows as $row){
            $row['role_id'] = (int)$row['role_id'];
            $row['create_at'] = (int)$row['create_at'];
            
            $role = new Roles();
            $role_detail = $role->find($row['role_id']);
            
            $row['role_name'] = $role_detail['role_name'];
            $data[] = $row;
        }
        return $data;
    }
    
    public function create(){
        $this->Id = randId(26);
        $this->f_name = htmlspecialchars(strip_tags($this->f_name));
        $this->l_name = htmlspecialchars(strip_tags($this->l_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone_no = htmlspecialchars(strip_tags($this->phone_no));
        $this->role_id = htmlspecialchars(strip_tags($this->role_id));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->create_at = time();
        $qry = "INSERT INTO ".$this->table_name."(Id,f_name,l_name,email,phone_no,role_id,password,create_at) VALUES(?,?,?,?,?,?,?,?)";
        $stmt = self::$conn->prepare($qry);
        $res = $stmt->execute(array(
            $this->Id,
            $this->f_name,
            $this->l_name,
            $this->email,
            $this->phone_no,
            $this->role_id,
            $this->password,
            $this->create_at));
        return $res;
    }
    
    public function update(){
        // sanitize all inputs
        $this->Id = htmlspecialchars(strip_tags($this->Id));
        $this->f_name = htmlspecialchars(strip_tags($this->f_name));
        $this->l_name = htmlspecialchars(strip_tags($this->l_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone_no = htmlspecialchars(strip_tags($this->phone_no));
        $this->role_id = htmlspecialchars(strip_tags($this->role_id));
        $this->password = htmlspecialchars(strip_tags($this->password));
        
        $qry = "UPDATE ".$this->table_name." SET "
                . "f_name = :f_name,"
                . "l_name = :l_name,"
                . "email = :email,"
                . "phone_no = :phone_no,"
                . "password = :password,"
                . "role_id = :role_id "
                . "where Id = :Id";
        try{
            $stmt = self::$conn->prepare($qry);
            $stmt->bindParam(':f_name', $this->f_name);
            $stmt->bindParam(':l_name', $this->l_name);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':phone_no', $this->phone_no);
            $stmt->bindParam(':password', $this->password);
            $stmt->bindParam(':role_id', $this->role_id);
            $stmt->bindParam(':Id', $this->Id);
            $res = $stmt->execute();
            return $res;
        }catch(Exception $e){
            return false;
        }
        
    }
    
    public function delete(){
        $qry = "DELETE FROM ".$this->table_name." WHERE Id = ?";
        //senitizing input
        $this->Id = htmlspecialchars(strip_tags($this->Id));
        $stmt = self::$conn->prepare($qry);
        $stmt->bindParam(1,$this->Id);
        return $stmt->execute();
    }
    
    public function isEmailAlreadyExist($email){
        $res = $this->read(" where email = '$email' ");
        if(count($res)>0){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function getUserRoleId($user_id){
        $qry = "SELECT role_id from ".$this->table_name." where Id = :user_id";
        $stmt = self::$conn->prepare($qry);
        $stmt->bindParam(':user_id',$user_id);
        $stmt->execute();
        if($stmt->rowCount()){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['role_id'];
        }
        else{
            return null;
        }
    }
    
}
