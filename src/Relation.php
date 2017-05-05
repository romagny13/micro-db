<?php
namespace MicroPHP\Db;


class Relation
{
    protected $table;
    protected $foreignAndPrimaryKeyPairs = [];
    // object infos
    protected $className;
    protected $propertyName;
    protected $modelInfos;

    public function __construct($table, array $foreignAndPrimaryKeyPairs, $className, $propertyName)
    {
        if(!class_exists($className)){ throw new \Exception('Class '. $className . ' not found'); }
        $this->modelInfos = new $className();
        $this->table = $table;
        $this->foreignAndPrimaryKeyPairs = $foreignAndPrimaryKeyPairs;
        $this->className = $className;
        $this->propertyName = $propertyName;
    }

    /**
     * returns the foreign key related table (example: user)
     *
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Returns only the foreign keys from mapping
     *
     * @return array
     */
    public function getFKeys()
    {
        $result = [];
        foreach ($this->foreignAndPrimaryKeyPairs as $fk=>$pk){
            array_push($result,$fk);
        }
        return $result;
    }

    public function getPrimaryAndForeignKeyPairs()
    {
        return $this->foreignAndPrimaryKeyPairs;
    }

    public function getModelInfos()
    {
        return $this->modelInfos;
    }


    /**
     *  example: 'App\User::class', required for instance creation
     *
     * @return mixed
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Get the columns from the related model
     * 
     * @return mixed
     */
    public function getColumns()
    {
        return $this->modelInfos->getColumns();
    }

    /**
     * 
     * 
     * @return mixed
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }
    
    /**
     * Returns the string condition to select the related row (example: users.id=1 when in posts table the foreign key is posts.user_id=1)
     * 
     * @param $model
     * @return mixed
     */
    public function getCondition($model){
      return QueryBuilderHelper::getForeignKeyConditionString($this->foreignAndPrimaryKeyPairs,$model);
    }
    
}