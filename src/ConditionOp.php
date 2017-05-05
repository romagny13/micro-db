<?php

namespace MicroPHP\Db;


class ConditionOp extends Condition
{
    public $operator;
    public $value;

    public function __construct($column,$operator, $value)
    {
        parent::__construct($column);
        $this->operator = $operator;
        $this->value = $value;
    }

    public function __toString()
    {
        return QueryBuilderHelper::explodeWithBackticks($this->column) . $this->operator.QueryBuilderHelper::getTypedValue($this->value). $this->appendOrderedConditions();
    }
    
    
}