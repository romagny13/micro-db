<?php

namespace MicroPHP\Db;
use PDO;


/**
 * Query with params
 *
 * Class PreparedQuery
 */
class PreparedQuery extends Query{

    protected function getParamType($value){
        if(is_int($value)){
            return PDO::PARAM_INT;
        }
        else if(is_bool($value)){
            return PDO::PARAM_BOOL;
        }
        else if(is_null($value)){
            return PDO::PARAM_NULL;
        }
        else {
            return PDO::PARAM_STR;
        }
    }
    
    /**
     * @param $name (example: ':id')
     * @param $value
     * @return $this
     */
    public function setParam($name,$value){
        $type = $this->getParamType($value);
        $this->statement->bindValue($name,$value, $type);
        return $this;
    }

    /**
     * @param null $values values array (used if no parameter defined with setParam function)
     * @return bool
     */
    public function execute($values=null){
        // params => ? ... values array required
        // or  params => :id + value with setParam and without values in execute
        return $this->statement->execute($values);
    }

    public function fetch(){
        $this->execute();
        return $this->statement->fetch();
    }

    public function fetchObject($className){
        $this->execute();
        return $this->statement->fetchObject($className);
    }

    public function fetchAll(){
        $this->execute();
        return $this->statement->fetchAll();
    }

    public function fetchAllWithClass($className){
        $this->execute();
        return $this->statement->fetchAll(PDO::FETCH_CLASS, $className);
    }
}
