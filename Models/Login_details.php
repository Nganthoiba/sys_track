<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Login_details
 *
 * @author Nganthoiba
 */
class Login_details extends Model{
    public $token_id;
    public $login_time;
    public $logout_time;
    public $user_id;
    public $client_ip;
    public $device_name;
    public static $table;
    //put your code here
    public function __construct() {
        parent::__construct();
        $this->table_name="login_details";
        self::$table = $this->table_name;
    }
    public function find($id){
        /*this will find only those who are currently logged in*/
        $res = $this->read("where token_id = ".$id." and logout_time = '0000-00-00 00:00:00' ");
        if(count($res)){
            $row = $res[0];
            $login_detail = new Login_details();
            $login_detail->token_id = $row['token_id'];
            $login_detail->user_id = $row['user_id'];
            $login_detail->login_time = $row['login_time'];
            $login_detail->logout_time = $row['logout_time'];
            $login_detail->client_ip = $row['client_ip'];
            $login_detail->device_name = $row['device_name'];
            return $login_detail;
        }
        else{
            return null;
        }
    }
    public function read($cond=""){
        $qry = "select * from ".$this->table_name." ".$cond;
        $res = self::$conn->query($qry);
        return $res->fetchall(PDO::FETCH_ASSOC);
    }
    
    public function create(){
        if($this->user_id == "" || $this->user_id==null || $this->token_id==null || $this->token_id==""){
            return false;
        }
//        $this->login_time = 'CURRENT_TIMESTAMP';
        $this->logout_time = '0000-00-00 00:00:00';
        $this->client_ip = get_client_ip();
        $this->device_name = filter_input(INPUT_SERVER,'HTTP_USER_AGENT');
        
        $qry = "INSERT INTO ".$this->table_name." (token_id,user_id,login_time,logout_time,client_ip,device_name) "
                . "VALUES(?,?,?,?,?,?)";
        $stmt = self::$conn->prepare($qry);
        $res = $stmt->execute(array(
            $this->token_id,$this->user_id,$this->login_time,$this->logout_time,$this->client_ip,$this->device_name
        ));
        return $res;
    }
    /*** this will not delete data actually but sets the logout time for a user ***/ 
    public function delete(){
        $qry = "UPDATE ".$this->table_name." SET  logout_time = CURRENT_TIMESTAMP WHERE token_id = ?";
        $stmt = self::$conn->prepare($qry);
        $stmt->bindParam(1, $this->token_id);
        return $stmt->execute();
    }
    
    public static function isValidToken($token){
        new Login_details();
        $qry = "select * from ".self::$table." where token_id = :token_id and logout_time='0000-00-00 00:00:00' ";
        $statement = self::$conn->prepare($qry);
        $statement->bindParam(':token_id',$token);
        $statement->execute();
        return ($statement->rowCount()>0);
    }
    /*** method to get user id by using the authorization token *****/
    public static function getUserId($token){
        $qry = "select * from ".self::$table." where token_id = :token_id and logout_time='0000-00-00 00:00:00' ";
        $statement = self::$conn->prepare($qry);
        $statement->bindParam(':token_id',$token);
        $statement->execute();
        if($statement->rowCount()>0){
            $rows = $statement->fetchall(PDO::FETCH_ASSOC);
            return $rows[0]['user_id'];
        }
        return null;
    }
}
