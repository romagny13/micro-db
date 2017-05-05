<?php

namespace MicroPHP\Db;


class ConditionIn extends Condition
{
    public $operator;
    public $values;

    public function __construct($column,$values)
    {
        parent::__construct($column);
        $this->values = $values;
    }

    public function __toString()
    {
        return QueryBuilderHelper::explodeWithBackticks($this->column) .' in ('. QueryBuilderHelper::joinWithTypedValue(',', $this->values) . ')';
    }
}