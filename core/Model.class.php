<?php

class Model 
{
    protected $_model;
    protected $_table;

    public function __construct()
    {   
        $this->dbConnect();
        $this->_model = get_class($this);
        $this->_model = substr($this->_model, 0, -5);
        $this->_table = strtolower($this->_model);
    }   

    public function dbCreate( $key, $db ) {
        if ($key == 'create_academic_year') {
            $conn = new PDO('mysql:host='. DB_HOST .';port=3306;', DB_USER, DB_PASSWORD);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql  = "CREATE DATABASE `". $db ."` ";
            $sql .= "DEFAULT CHARACTER SET utf8 ";
            $sql .= "DEFAULT COLLATE utf8_general_ci;";
            $conn->exec($sql);
            return 1;
        }
    }

    public function dbQuery( $key, $data ) { 

    }   

    public function dbSelect( $sql, $arr=array() ) { 
        $str = $this->pdo->prepare($sql);
        if (empty($arr)) {
          $str->execute();
        } else {
          $str->execute($arr);                                                  
        }   
        return $str->fetchAll(PDO::FETCH_ASSOC);
    }   

    public function dbUpdate( $sql, $arr=array() ) { 
        $str = $this->pdo->prepare($sql);
        if (empty($arr)) {
          $str->execute();
        } else {
          $str->execute($arr);
        }   
        return $str->rowCount();
    }   

    private function dbConnect() {
        $this->pdo = new PDO('mysql:host='. DB_HOST .';port=3306;dbname='. DB_NAME .';charset=utf8', DB_USER, DB_PASSWORD, array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));
    }   
}
