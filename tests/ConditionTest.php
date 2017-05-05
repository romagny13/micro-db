<?php

use \MicroPHP\Db\Condition;

class ConditionTest  extends PHPUnit_Framework_TestCase
{

    public function testConditionOp_WithNoOperator_IsEquals(){
        $result = Condition::op('id',10);
        $this->assertEquals('id', $result->column);
        $this->assertEquals('=', $result->operator);
        $this->assertEquals(10, $result->value);
    }

    public function testConditionOp_WithOperator(){
        $result = Condition::op('id','>',10);
        $this->assertEquals('id', $result->column);
        $this->assertEquals('>', $result->operator);
        $this->assertEquals(10, $result->value);
    }

    public function testOrderedCondition_WithOperator(){
        $result = (string) Condition::op('a','<',10)->_and_(Condition::op('b','>',20));
        $this->assertEquals('`a`<10 and `b`>20',$result);
    }

    public function testOrderedCondition_WithString(){
        $result = (string) Condition::op('a','<',10)->_and_('b>20');
        $this->assertEquals('`a`<10 and b>20',$result);
    }

    public function testOrderedCondition_WithMultipleConditions(){
        $result = (string) Condition::op('a','<',10)
            ->_and_(Condition::op('b','>',20))
            ->_or_(Condition::between('c',60,100));
        $this->assertEquals('`a`<10 and `b`>20 or `c` between 60 and 100',$result);
    }
    
}