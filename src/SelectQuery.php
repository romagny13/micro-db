<?php

namespace MicroPHP\Db;

use \Exception;

class SelectQuery
{
    protected $columns = []; // select
    protected $statements = []; // distinct
    protected $tables = []; // from
    protected $condition; // where
    protected $limiter; // limit
    protected $sorters = []; // order by
    protected $hasOptions = false;
    protected $hasFrom = false;
    protected $hasWhere = false;
    protected $hasLimit = false;
    protected $hasOrderBy = false;

    public function __construct($columns)
    {
        $this->columns = $columns;
    }

    /**
     * Add options (example: DISTINCT)
     *
     * @param array ...$statements
     * @return $this
     * @throws Exception
     */
    public function options(...$statements){
        if($this->hasOptions) { throw new Exception('Options already defined');}
        $this->statements = $statements;
        $this->hasOptions = true;
        return $this;
    }

    /**
     * @param array ...$tables table names (example: 'posts','users')
     * @return $this
     * @throws Exception
     */
    public function from(...$tables){
        if($this->hasFrom) { throw new Exception('One from clause');}
        $this->tables = $tables;
        $this->hasFrom = true;
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

    /**
     *
     * @param $offsetOrMaxRows max rows if second parameter is null
     * @param null $maxRows
     * @return $this
     * @throws Exception
     */
    public function limit($offsetOrMaxRows,$maxRows=null){
        if($this->hasLimit) { throw new Exception('One limit clause');}
        $this->limiter = isset($maxRows)? $offsetOrMaxRows . ',' . $maxRows: $offsetOrMaxRows;
        $this->hasLimit = true;
        return $this;
    }

    /**
     * @param array ...$sorters strings (example 'id','title desc') or with Sort (example: Sort::asc('id'),Sort::desc('title'))
     * @return $this
     * @throws Exception
     */
    public function orderBy(...$sorters){
        if($this->hasOrderBy) { throw new Exception('One order by clause');}
        $this->sorters = $sorters;
        $this->hasOrderBy = true;
        return $this;
    }

    public function build(){
        return QueryBuilderHelper::getSelectFromString($this->tables,$this->columns, $this->statements)
        .QueryBuilderHelper::getWhereString($this->condition)
        .QueryBuilderHelper::getOrderByString($this->sorters)
        .QueryBuilderHelper::getLimitString($this->limiter);
    }


    public function setClassName($className){
        $this->className = $className;
        return $this;
    }
    
    public function fetch($className=null){
        // build la request
        $queryString = $this->build();

        // executer
        if(isset($className)){
            return Db::getInstance()->query($queryString)->fetchObject($className);
        }
        else {
            return Db::getInstance()->query($queryString)->fetch();
        }
    }

    public function fetchAll($className=null){
        // build la request
        $queryString = $this->build();

        // executer
        if(isset($className)){
            return Db::getInstance()->query($queryString)->fetchAllWithClass($className);
        }
        else {
            return Db::getInstance()->query($queryString)->fetchAll();
        }
    }

}
