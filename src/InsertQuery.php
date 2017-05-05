<?php

namespace MicroPHP\Db;

use \Exception;

class InsertQuery
{
    protected $table;
    protected $table_columns = [];
    protected $table_values = [];
    protected $hasColumns = false;
    protected $hasValues = false;

    public function __construct($table)
    {
        $this->table= $table;
    }

    /**
     * @param array ...$columns columns to add new values (example: 'title','content','user_id')
     * @return $this
     * @throws Exception
     */
    public function columns(...$columns){
        if($this->hasColumns) { throw new Exception('Columns already set');}
        $this->table_columns = $columns;
        return $this;
    }

    /**
     * @param array ...$values values to insert
     * @return $this
     * @throws Exception
     */
    public function values(...$values){
        if($this->hasValues) { throw new Exception('Values already set');}
        $this->table_values = $values;
        return $this;
    }

    /**
     * @return string the query string
     */
    public function build(){
        return QueryBuilderHelper::getInsertIntoString($this->table,$this->table_columns, $this->table_values);
    }


    public function execute(array $values=null){
        if(!isset($values) && count($this->table_values) === 0) { throw new Exception('No values provided');}

        // build la request
        $queryString = $this->build();

        return Db::getInstance()->prepare($queryString)->execute($values);
    }
}