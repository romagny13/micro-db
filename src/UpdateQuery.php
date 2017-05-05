<?php

namespace MicroPHP\Db;

use \Exception;

class UpdateQuery
{
    public $table;
    public $columnValues = []; // set
    protected $condition; // where
    protected $hasSet = false;
    protected $hasWhere = false;

    public function __construct($table)
    {
        $this->table= $table;
    }

    /**
     * @param array $columnValues column with new values (example: ['title'=>'new title','content'=>'new content']
     * @return $this
     * @throws Exception
     */
    public function set(array $columnValues){
        if($this->hasWhere) { throw new Exception('One set clause');}
        $this->columnValues = $columnValues;
        return $this;
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
        return QueryBuilderHelper::getUpdateString($this->table,$this->columnValues,$this->condition);
    }

    public function execute(){
        // build la request
        $queryString = $this->build();

        return Db::getInstance()->query($queryString)->execute();
    }

}