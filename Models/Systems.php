<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Systems
 *
 * @author Nganthoiba
 */
class Systems extends Model{
    
    public $Id ;              
    public $sys_no;        
    public $sys_name;           
    public $ip;              
    public $brand ;            
    public $sys_type;        
    public $model_no;         
    public $cpu;                
    public $ram_size;           
    public $hdd_size;         
    public $lab_no;
    public $created_by;
    
    public function __construct() {
        parent::__construct();
        $this->table_name = "systems";
    }
    public function find($id = ""){
        if($id === ""){
            return null;
        }
        else{
            $id = htmlspecialchars(strip_tags($id));
        }
        $rows = $this->read("where Id = '$id'");
        if(count($rows)==0){
            return null;
        }
        $rows[0]['sys_no'] = (int)$rows[0]['sys_no'];
        $rows[0]['lab_no'] = (int)$rows[0]['lab_no'];
        return $rows[0];
    }
    public function create(){
        $this->Id= randId(26);
        $this->sys_no = htmlspecialchars(strip_tags($this->sys_no));
        $this->sys_name = htmlspecialchars(strip_tags($this->sys_name));
        $this->ip = htmlspecialchars(strip_tags($this->ip));
        $this->brand = htmlspecialchars(strip_tags($this->brand));
        $this->sys_type = htmlspecialchars(strip_tags($this->sys_type));
        $this->model_no = htmlspecialchars(strip_tags($this->model_no));
        $this->cpu = htmlspecialchars(strip_tags($this->cpu));
        $this->ram_size = htmlspecialchars(strip_tags($this->ram_size));
        $this->hdd_size = htmlspecialchars(strip_tags($this->hdd_size));
        $this->lab_no = htmlspecialchars(strip_tags($this->lab_no));
        $this->created_by = htmlspecialchars(strip_tags($this->created_by));
        try{
            $qry = "insert into ".$this->table_name." (Id,sys_no,sys_name,ip,brand,sys_type,model_no,cpu,ram_size,hdd_size,lab_no,created_by)"
                    . "values(:Id,:sys_no,:sys_name,:ip,:brand,:sys_type,:model_no,:cpu,:ram_size,:hdd_size,:lab_no,:created_by)";
            $stmt = self::$conn->prepare($qry);
            $stmt->bindParam(':Id', $this->Id);
            $stmt->bindParam(':sys_no', $this->sys_no);
            $stmt->bindParam(':sys_name', $this->sys_name);
            $stmt->bindParam(':ip', $this->ip);
            $stmt->bindParam(':brand', $this->brand);
            $stmt->bindParam(':sys_type', $this->sys_type);
            $stmt->bindParam(':model_no', $this->model_no);
            $stmt->bindParam(':cpu', $this->cpu);
            $stmt->bindParam(':ram_size', $this->ram_size);
            $stmt->bindParam(':hdd_size', $this->hdd_size);
            $stmt->bindParam(':lab_no', $this->lab_no);
            $stmt->bindParam(':created_by', $this->created_by);
            if($stmt->execute()){
                return true;
            }
        }catch(Exception $e){
            throw $e;
        }
        return false;
    }
    public function read($cond = ""){
        $qry = "select * from ".$this->table_name." ".$cond;
        $res = self::$conn->query($qry);
        $rows = $res->fetchall(PDO::FETCH_ASSOC);
        $data = array();
        foreach ($rows as $row){
            $row['sys_no'] = (int)$row['sys_no'];
            $row['lab_no'] = (int)$row['lab_no'];
            $data[] = $row;
        }
        return $data;
    }
    public function update(){
        $this->sys_no = htmlspecialchars(strip_tags($this->sys_no));
        $this->sys_name = htmlspecialchars(strip_tags($this->sys_name));
        $this->ip = htmlspecialchars(strip_tags($this->ip));
        $this->brand = htmlspecialchars(strip_tags($this->brand));
        $this->sys_type = htmlspecialchars(strip_tags($this->sys_type));
        $this->model_no = htmlspecialchars(strip_tags($this->model_no));
        $this->cpu = htmlspecialchars(strip_tags($this->cpu));
        $this->ram_size = htmlspecialchars(strip_tags($this->ram_size));
        $this->hdd_size = htmlspecialchars(strip_tags($this->hdd_size));
        $this->lab_no = htmlspecialchars(strip_tags($this->lab_no));
        try{
            $qry = "update ".$this->table_name." set Id = :Id,"
                    . "sys_no = :sys_no,"
                    . "sys_name = :sys_name,ip = :ip,brand = :brand,sys_type = :sys_type,model_no = :model_no,"
                    . "cpu = :cpu,ram_size = :ram_size,hdd_size = :hdd_size,lab_no = :lab_no "
                    . "where Id = :Id";
            $stmt = self::$conn->prepare($qry);
            $stmt->bindParam(':Id', $this->Id);
            $stmt->bindParam(':sys_no', $this->sys_no);
            $stmt->bindParam(':sys_name', $this->sys_name);
            $stmt->bindParam(':ip', $this->ip);
            $stmt->bindParam(':brand', $this->brand);
            $stmt->bindParam(':sys_type', $this->sys_type);
            $stmt->bindParam(':model_no', $this->model_no);
            $stmt->bindParam(':cpu', $this->cpu);
            $stmt->bindParam(':ram_size', $this->ram_size);
            $stmt->bindParam(':hdd_size', $this->hdd_size);
            $stmt->bindParam(':lab_no', $this->lab_no);
            if($stmt->execute()){
                return true;
            }
        }catch(Exception $e){
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
}
