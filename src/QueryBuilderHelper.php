<?php

namespace MicroPHP\Db;

use \Exception;

class QueryBuilderHelper
{
    public static function getTypedValue($value){
        if(is_string($value)){
            return '\'' . $value . '\'';
        }
        else {
            return $value;
        }
    }
    
    public static function wrapWithBacktick($value){
        $settings = Db::getSettings();
        if(isset($settings) && isset($settings['start']) && isset($settings['end'])){
            return $settings['start'] . $value . $settings['end'];
        }
        return '`' . $value . '`';
    }

    public static function explodeWithBackticks($value, $delimiter='.'){
        // 'posts.id'
        // => explode 'posts' 'id'
        // => wrap with back tick 
        // => join with .
        $parts = explode($delimiter, $value);
        $result = [];
        foreach ($parts as $part){
            array_push($result,self::wrapWithBacktick($part));
        }
        return join('.',$result);
    }
    
    public static function getOptionString($options=[]){
        return count($options)> 0 ? join(' ',$options). ' ': '';
    }
    
    public static function joinWithBacktick($glue, $array){
        $result = [];
        foreach ($array as $value){
            array_push($result, self::explodeWithBackticks($value));
        }
        return join($glue, $result);
    }

    public static function joinWithTypedValue($glue, $array){
        $result = [];
        foreach ($array as $value){
            array_push($result, self::getTypedValue($value));
        }
        return join($glue, $result);
    }

    public static function getColumnsString(array $columns){
        return self::joinWithBacktick(',', $columns);
    }

    public static function getSelectColumnsString(array $columns){
        if($columns === ['*']){
            return '*';
        }
        else {
            return self::getColumnsString($columns);
        }
    }

    public static function getSelectFromString(array $tables,array $columns, $options=[]){
        if(count($tables) === 0){ throw new Exception('Table name required'); }
        return 'select ' . self::getOptionString($options) . self::getSelectColumnsString($columns). ' from '. self::joinWithBacktick(',', $tables);
    }

    public static function getWhereString($condition){
        if(is_string($condition) && trim($condition) !==''){
            // where posts.user_id=users.id
            return ' where ' . $condition;
        }
        else if($condition instanceof Condition){
            return ' where ' . (string)$condition;
        }
        return '';
    }

    public static function explodeOrderByString($sort){
        $parts = explode(' ', $sort); // 0=> id 1=> desc
        return isset($parts[1])? self::explodeWithBackticks($parts[0]). ' '.$parts[1]: self::explodeWithBackticks($parts[0]);
    }

    public static function getOrderByString(array $sorts){
        $result = [];
        if(count($sorts)> 0) {
            foreach ($sorts as $sort){
                if($sort instanceof Sort){
                    array_push($result, (string)$sort);
                }
                else {
                    array_push($result, self::explodeOrderByString($sort));
                }
            }
            return ' order by ' . join(',', $result);
        }
        return '';
    }

    public static function getLimitString($value){
        if(isset($value) && trim(strval($value)) !==''){
            return ' limit '. $value;
        }
        return '';
    }

    public static function getValuesString($columnCount,$values){
        if($columnCount === 0){ throw new Exception('No values provided');}
        $result = [];
        if(count($values)>0) {
            foreach ($values as $value) {
                array_push($result, self::getTypedValue($value));
            }
        }
        else {
            // => ?,?
            for ($i = 0; $i < $columnCount; $i++) {
                array_push($result, '?');
            }
        }
        return join(',', $result);
    }

    public static function getInsertIntoString($table, array $columns,$values=[]){
        return 'insert into '. self::explodeWithBackticks($table) .' ('. self::getColumnsString($columns).') values (' . self::getValuesString(count($columns), $values) . ')';
    }

    public static function getSetString(array $columnValues){
        // if(count($columnValues) === 0){ throw new \Exception('No column found for the request'); }
        $result = [];
        foreach ($columnValues as $column=>$value){
            array_push($result, self::explodeWithBackticks($column) . '=' . self::getTypedValue($value));
        }
        return join(',', $result);
    }

    public static function getUpdateString($table, array $columnValues, $condition){
        return 'update '. self::explodeWithBackticks($table) .' set '. self::getSetString($columnValues) . QueryBuilderHelper::getWhereString($condition);
    }

    public static function getDeleteFromString($table, $condition){
        return 'delete from '. self::explodeWithBackticks($table) . QueryBuilderHelper::getWhereString($condition) ;
    }

    public static function getForeignKeyConditionString($keys,$model){
        $result = [];
        // posts.user_id (fk) => users.id (pk)
        foreach ($keys as $fk=>$pk){
            $value = $model->{$fk};
            array_push($result, self::explodeWithBackticks($pk) .'=' . self::getTypedValue($value));
        }
        return join(' and ', $result);
    }

    public static function isNull($keys,$model){
        foreach ($keys as $key){
            if(!isset($model->{$key})){
                return true;
            }
        }
        return false;
    }
}