<?php

namespace MicroPHP\Db;


class ConditionLike extends Condition
{
    public $value;

    public function __construct($column, $value)
    {
        parent::__construct($column);
        $this->value = $value;
    }

    public function __toString()
    {
        return QueryBuilderHelper::explodeWithBackticks($this->column) .' like \''. $this->value . '\'';
    }
}