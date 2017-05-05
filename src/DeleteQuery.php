<?php

namespace MicroPHP\Db;


use \Exception;

class DeleteQuery
{
    public $table;
    protected $condition; // where
    protected $hasWhere = false;

    public function __construct($table)
    {
        $this->table= $table;
    }

    /**
     * @param $condition string (example: 'id=10') or Condition (example: Condition::op('id','=',10))
     * @return $this
     * @throws Exception
     */
    public function where($condition){
        if($this->hasWhere) { throw new Exception('One where clause');}
        $this->condition = $condition;
        $this->hasWhere = true;
        return $this;
    }
    
    public function build(){
        return QueryBuilderHelper::getDeleteFromString($this->table,$this->condition);
    }
    
    public function execute(){
        // build la request
        $queryString = $this->build();

        return Db::getInstance()->query($queryString)->execute();
    }
}