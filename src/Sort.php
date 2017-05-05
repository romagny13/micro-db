<?php

namespace MicroPHP\Db;

class Sort
{
    public $column;
    public $direction;

    public function __construct($column,$direction ='')
    {
        $this->direction = $direction;
        $this->column = $column;
    }

    public static function asc($column){
        return new Sort($column);
    }
    public static function desc($column){
        return new Sort($column, 'desc');
    }

    public function  __toString()
    {
        return $this->direction === '' ? QueryBuilderHelper::explodeWithBackticks($this->column):   QueryBuilderHelper::explodeWithBackticks($this->column). ' ' . $this->direction;
    }
}