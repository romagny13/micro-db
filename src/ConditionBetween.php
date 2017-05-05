<?php
namespace MicroPHP\Db;


class ConditionBetween extends Condition
{
    public $value1;
    public $value2;

    public function __construct($column, $value1, $value2)
    {
        parent::__construct($column);
        $this->value1 = $value1;
        $this->value2 = $value2;
    }

    public function __toString()
    {
        return QueryBuilderHelper::explodeWithBackticks($this->column) .' between '. $this->value1 . ' and ' . $this->value2;
    }
}