<?php

namespace MicroPHP\Db;


class Condition
{
    public $column;
    public $orderedConditions= [];
    
    public function __construct($column)
    {
        $this->column = $column;
    }

    public  function _and_($condition){
        $condition = new OrderedCondition('and',$condition);
        array_push($this->orderedConditions, $condition);
        return $this;
    }
    
    public  function _or_($condition){
        $condition = new OrderedCondition('or',$condition);
        array_push($this->orderedConditions, $condition);
        return $this;
    }

    public  function appendOrderedConditions(){
        $result ='';
        foreach ($this->orderedConditions as $orderedCondition){
            $result .= (string)$orderedCondition;
        }
        return $result;
    }

    /**
     * @param $column
     * @param $operatorOrValue without operator, operator is '='
     * @param $value
     * @return ConditionOp
     */
    public static function op($column, $operatorOrValue, $value=null){
        if(!isset($value)){
            $value = $operatorOrValue;
            $operatorOrValue = '=';
        }
        return new ConditionOp($column,$operatorOrValue, $value);
    }

    public static function like($column, $value){
        return  new ConditionLike($column,$value);
    }

    public static function in($column,array $values){
        return  new ConditionIn($column,$values);
    }

    public static function between($column, $value1, $value2){
        return  new ConditionBetween($column,$value1,$value2);
    }
}