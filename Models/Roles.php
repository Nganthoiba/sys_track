<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Roles
 *
 * @author Nganthoiba
 */
class Roles extends Model{
    //put your code here
    public function __construct() {
        parent::__construct();
        $this->table_name = "roles";
    }
    
    public function read($cond = ""){
        $qry = "select * from ".$this->table_name." ".$cond;
        $res = self::$conn->query($qry);
        return $res->fetchall(PDO::FETCH_ASSOC);
    }
    
    public function find($id){
        $result = $this->read("where Id = '$id' ");
        if(count($result)>0){
            return $result[0];
        }
        else{
            return null;
        }
    }
}
