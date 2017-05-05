<?php
namespace MicroPHP\Db;


class OrderedCondition
{
    public $operator; // and | or
    public $condition;
    
    public function __construct($operator, $condition)
    {
        $this->operator = $operator;
        $this->condition = $condition;
    }

    public function __toString()
    {
       return ' '.$this->operator.' '.  (string) $this->condition;
    }
}