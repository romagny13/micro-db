<?php

namespace MicroPHP\Db;

use PDO;
use PDOStatement;

class SimpleQuery extends Query{

    public function execute(){
        return $this->statement->execute();
    }

    public function fetch(){
        return $this->statement->fetch();
    }

    public function fetchObject($className){
        return $this->statement->fetchObject($className);
    }

    public function fetchAll(){
        return $this->statement->fetchAll();
    }

    public function fetchAllWithClass($className){
        return $this->statement->fetchAll(PDO::FETCH_CLASS, $className);
    }
}