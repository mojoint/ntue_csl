<?php

class Model 
{
    protected $_model;
    protected $_pdo;

    public function __construct()
    {   
        $this->dbConnect();
        $this->_model = get_class($this);
        $this->_model = substr($this->_model, 0, -5);
    }   

    public function dbQuery( $key, $data ) { 

    }   

    public function dbSelect( $sql, $arr ) { 
        $str = $this->_pdo->prepare($sql);
        if (empty($arr)) {
            $str->excute();
        } else {
            $str->execute($arr);    
        }
        return $str->fetchAll(PDO::FETCH_ASSOC);
    }   

    public function dbInsert( $sql, $arr ) { 
        $str = $this->_pdo->prepare($sql);
        $str->execute($arr);
        return $this->_pdo->lastInsertId();
    }   

    public function dbUpdate( $sql, $arr ) { 
        $str = $this->_pdo->prepare($sql);
        $str->execute($arr);
        return $str->rowCount();
    }  

    public function dbLogger($path, $key, $val, $data) {
        $sql = 'INSERT INTO `logger` (`id`, `path`, `key`, `val`, `data`) VALUES (0, :path, :key, :val, :data)';
        return $this->dbInsert( $sql, array(':path'=>$path, ':key'=>$key, ':val'=>$val, ':data'=>$data ) );
    }  

    private function dbConnect() {
        $this->_pdo = new PDO('mysql:host='. DB_HOST .';port=3306;dbname='. DB_NAME .';charset=utf8', DB_USER, DB_PASSWORD);
        $this->_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
        $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }   
}
