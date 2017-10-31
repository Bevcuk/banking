<?php
require_once 'error_handler.php';

class Config {
    public $DataBaseHandler;
    
    public function __construct() {
        $this->intDbHandler();
    }
    
    private function intDbHandler() {
        try {  
            $this->DataBaseHandler = new PDO("mysql:host=localhost;dbname=online_banking", 'root', 'admin', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $this->DataBaseHandler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->DataBaseHandler->setAttribute(PDO::ATTR_EMULATE_PREPARES, false );           
        } catch(PDOException $e) {		
            process_error($e);
        }	
    }
}
?>