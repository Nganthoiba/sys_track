<?php
/**
 * Description of DbConnect
 *
 * @author Nganthoiba
 */
class DbConnect extends PDO{
    public $con;
    //put your code here
    public function __construct($db_server,$db_name,$db_username,$db_password) {
        $this->con=parent::__construct("mysql:host=$db_server;dbname=$db_name", $db_username, $db_password);
    }
   
    public function beginTransaction() {
        parent::beginTransaction();
    }

    public function commit() {
        parent::commit();
    }
    
    public function rollBack() {
        parent::rollBack();
    }
    public function errorInfo() {
        parent::errorInfo();
    }
    public function errorCode() {
        parent::errorCode();
    }
    
    public function exec_query($qry){
        return $this->query($qry);
    }
}
