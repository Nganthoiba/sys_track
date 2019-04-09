<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Posts
 *
 * @author Nganthoiba
 */
class Posts extends Model{
    
    public $Id;       //message ID
    public $message; //text messages      
    //public $complain_id; //to show under which complain the message is posted       
    public $user_id;  // who post the message       
    public $create_at;  //when the message was posted       
    public $update_at;   //when the message is updated       
    public $delete_at;//when the message is deleted
    //put your code here
    public function __construct() {
        parent::__construct();
        $this->table_name = "posts";
    }
    
    public function create(){
        $this->Id = randId(26);
        $this->message = htmlspecialchars(strip_tags($this->message));
        //$this->complain_id = htmlspecialchars(strip_tags($this->complain_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->create_at = time();
        $this->update_at = 0;
        $this->delete_at = 0;
        
        $qry = "insert into ".$this->table_name."(Id,message,user_id,create_at,update_at,delete_at)"
                . " values(:Id,:message,:user_id,:create_at,:update_at,:delete_at)";
        $stmt = self::$conn->prepare($qry);
        $stmt->bindParam(":Id",$this->Id);
        $stmt->bindParam(":message",$this->message);
        //$stmt->bindParam(":complain_id",$this->complain_id);
        $stmt->bindParam(":user_id",$this->user_id);
        $stmt->bindParam(":create_at",$this->create_at);
        $stmt->bindParam(":update_at",$this->update_at);
        $stmt->bindParam(":delete_at",$this->delete_at);
        return $stmt->execute();
    }
    
    public function read($cond=""){
        $cond = (trim($cond)=="")?"where delete_at=0 order by create_at asc":$cond;
        $qry = "select * from ".$this->table_name." ".$cond;
        $res = self::$conn->query($qry);
        $data = array();
        $user = new Users();
        while($row = $res->fetch(PDO::FETCH_ASSOC)){
            $row['create_at']=(int)$row['create_at'];
            $row['update_at']=(int)$row['update_at'];
            $row['delete_at']=(int)$row['delete_at'];
            $uer_det = $user->find($row['user_id']);
            if($uer_det!=null){
                $row['sender_name'] = $uer_det['f_name']." ".$uer_det['l_name'];
            }
            else{
                $row['sender_name'] = "Unknown";
            }
            $data[] = $row;
        }
        return $data;
    }
    
    public function delete($Id/*post id*/){
        $qry = "update ".$this->table_name." set delete_at=:delete_at where Id = :Id";
        $stmt = self::$conn->prepare($qry);
        $stmt->bindParam(":delete_at",time());
        $stmt->bindParam(":Id",$Id);
        return $stmt->execute();
    }
}
