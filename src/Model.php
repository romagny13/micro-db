<?php

namespace MicroPHP\Db;

class Model
{
    protected $table;
    protected $columns = ['*'];
    protected $relations  = [];
    
    public static function getModelName(){
        return get_called_class();
    }

    public static function createModel(){
        $class = self::getModelName();
        return new $class;
    }

    public function hasRelations(){
        return count($this->relations) > 0;
    }
    
    public function addRelation($table,array $foreignAndPrimaryKeyPairs,$className,$propertyName){
        $relation = new Relation($table,$foreignAndPrimaryKeyPairs,$className,$propertyName);
        array_push($this->relations,$relation);
        return $relation;
    }

    public function getTable(){
        if(!isset($this->table)){ throw new \Exception('No table found.');}
        return $this->table;
    }
    
    public  function  getColumns(){
        if($this->hasRelations()){
            // return columns + fks
            $result = [];
            foreach ($this->relations as $relation){
              $result = array_merge($result, $relation->getFKeys());
            }
            return array_merge($this->columns, $result);
        }
        return $this->columns;
    }

    public static function getRelatedModels($relations,$model){
        foreach ($relations as $relation) {
            // if model fk is not null
            if(!QueryBuilderHelper::isNull($relation->getFKeys(),$model)){
                // select id,username from users where id=1 ... (user_id of post)

                $queryString = Db::getInstance()
                    ->select(...$relation->getColumns())
                    ->from($relation->getTable())
                    ->where($relation->getCondition($model))
                    ->build();
                
                $inner = Db::getInstance()->prepare($queryString)->fetchObject($relation->getClassName());
                // add to model related object model
                $model->{$relation->getPropertyName()} = $inner;
            }
            else {
                $model->{$relation->getPropertyName()} = null;
            }
        }
        // return the model with related models
        return $model;
    }

    private static function doSelect($condition=null,$offsetOrMaxRows=null, $maxRows=null){
        // create model
        $model = self::createModel();

        // get table name and column names
        $table = $model->getTable();
        $columns= $model->getColumns();

        $query = isset($condition) ? Db::getInstance()->select(...$columns)->where($condition)->from($table): Db::getInstance()->select(...$columns)->from($table);
        if (isset($offsetOrMaxRows)) {
            $query = $query->limit($offsetOrMaxRows, $maxRows);
        }
        if($model->hasRelations()) {
            $result = [];
            $className = self::getModelName();
            $queryString = $query->build();
            $statement = Db::getInstance()->query($queryString);
            while ($row = $statement->fetchObject($className)) {
                //var_dump($row);
                array_push($result, self::getRelatedModels($model->relations, $row));
            }
            return $result;
        }
        else {
            return $query->fetchAll($model->getModelName());
        }
    }

    public static function all($offsetOrMaxRows=null, $maxRows=null){
        return self::doSelect(null,$offsetOrMaxRows,$maxRows);
    }
    
    public static function where($condition, $offsetOrMaxRows=null,$maxRows=null){
        return self::doSelect($condition,$offsetOrMaxRows,$maxRows);
    }

    /**
     * Find one or first match
     * 
     * @param $condition
     * @return mixed
     * @throws \Exception
     */
    public static function find($condition){
        // create model
        $model = self::createModel();

        // get table name and column names
        $table = $model->getTable();
        $columns= $model->getColumns();

        $result = Db::getInstance()
            ->select(...$columns)
            ->from($table)
            ->where($condition)
            ->fetch($model->getModelName());

        if($model->hasRelations()) {
           return self::getRelatedModels($model->relations, $result);
        }
        return $result;
    }

    public static function create($columnValues){
        // create model
        $model = self::createModel();

        // get table name
        $table = $model->getTable();

        $columns= [];
        $values = [];
        foreach ($columnValues as $key=>$value){
            array_push($columns, $key);
            array_push($values, $value);
        }

        return Db::getInstance()
            ->insert_into($table)
            ->columns(...$columns)
            ->execute($values);
    }

    public static function update($columnValues, $condition){
        // create model
        $model = self::createModel();

        // get table name
        $table = $model->getTable();

        return Db::getInstance()
            ->update($table)
            ->set($columnValues)
            ->where($condition)
            ->execute();
    }

    public static function delete($condition){
        // create model
        $model = self::createModel();

        // get table name
        $table = $model->getTable();

        return Db::getInstance()
            ->delete_from($table)
            ->where($condition)
            ->execute();
    }

    public static function query($sql){
       return Db::getInstance()->query($sql);
    }

    public static function prepare($sql){
        return Db::getInstance()->prepare($sql);
    }
    
}
