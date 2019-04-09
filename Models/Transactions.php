<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Transactions
 *
 * 
 */
class Transactions extends Model{
    public $complain_id;        
    public $user_id;       
    public $step_id;      
    public $create_at;
    //put your code here
    public function __construct() {
        parent::__construct();
        $this->table_name = "transactions";
    }
    
    public function create(){
        $this->create_at = time();
        $this->complain_id = htmlspecialchars(strip_tags($this->complain_id));
        $this->step_id = htmlspecialchars(strip_tags($this->step_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        try{
            $qry = "insert into ".$this->table_name." (complain_id,user_id,step_id,create_at) "
                    . "values(:complain_id,:user_id,:step_id,:create_at)";
            $stmt = self::$conn->prepare($qry);
            $stmt->bindParam(":complain_id",$this->complain_id);
            $stmt->bindParam(":user_id",$this->user_id);
            $stmt->bindParam(":step_id",$this->step_id);
            $stmt->bindParam(":create_at",$this->create_at);
            return $stmt->execute();
        } catch (Exception $e){
            throw $e;
        }
        return false;
    }
    
    public function read($cond=""){
        $qry = "select * from ".$this->table_name." ".$cond;
        $res = self::$conn->query($qry);
        $rows = array();
        //$rows = $res->fetchall(PDO::FETCH_ASSOC);
        while ($row = $res->fetch(PDO::FETCH_ASSOC)){
            $row['description']= $this->getStepDescription($row['step_id']);
            $row['create_at'] = (int)$row['create_at'];
            $row['step_id'] = (int)$row['step_id'];
            $rows[]=$row;
        }
        return $rows;
    }
    
    public static function isTransactionExist($complain_id,$step_id){
        $qry = "select * from transactions where complain_id='$complain_id' and step_id=$step_id";
        $res = self::$conn->query($qry);
        if($res->rowCount()>0){
            return true;
        }
        else{
            return false;
        }
    }
    /***** private method *****/
    private function getStepDescription($step_id){
        $qry = "select * from steps where id=".$step_id;
        $res = self::$conn->query($qry);
        if($res->rowCount()==0){
            return null;
        }
        else{
            $data = $res->fetch(PDO::FETCH_ASSOC);
            return $data['description'];
        }
    }
}
