<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of model
 *
 * @author Nganthoiba
 */
include_once ('DbConnect.php');
class Model {
    public static $conn; //database connection variable
    protected $table_name;
    public function __construct() {
        $this->table_name = null;
        $db_server = Config::get('db_server');
        $db_name = Config::get('db_name');
        $db_username = Config::get('db_username');
        $db_password = Config::get('db_password');
        self::$conn = new DbConnect($db_server, null, $db_username, $db_password);
        if(!self::$conn){
            throw new Exception("Failed to connect database server.");
        }
        $prepare_statement = self::$conn->prepare("CREATE DATABASE IF NOT EXISTS $db_name");
        //creates a database if it didn't exist before
        $res=$prepare_statement->execute();
        if($res){
            self::$conn = new DbConnect($db_server,$db_name, $db_username, $db_password);//here connects the database
            if(!self::$conn){
                throw new Exception("Failed to connect database.");
            }
        }
    }
}
