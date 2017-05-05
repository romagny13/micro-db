<?php

use \MicroPHP\Db\QueryBuilderHelper;
use \MicroPHP\Db\Sort;
use \MicroPHP\Db\Condition;
use \MicroPHP\Db\Db;

class QueryBuilderHelperTest  extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        Db::setTableAndColumnStrategy();
    }

    public function testGetTypedValue_WithString_ReturnsString(){
        $result = QueryBuilderHelper::getTypedValue('my value');
        $this->assertEquals('\'my value\'', $result);
    }

    public function testGetTypedValue_WithOtherValue_ReturnsValue(){
        $result = QueryBuilderHelper::getTypedValue(10);
        $this->assertEquals(10, $result);
    }

    // getSelectColumnsString

    public function testGetSelectColumnsString_WithAll_ReturnsAll(){
       $result = QueryBuilderHelper::getSelectColumnsString(['*']);
        $this->assertEquals('*', $result);
    }

    public function testGetSelectColumnsString_WithColumns_ReturnsColumnsWithBackTicks(){
        $result = QueryBuilderHelper::getSelectColumnsString(['id','title']);
        $this->assertEquals('`id`,`title`', $result);
    }

    // getSelectFromString

    public function testGetSelectFromString_WithAllColumns_ReturnsAll(){
        $result = QueryBuilderHelper::getSelectFromString(['posts'],['*']);
        $this->assertEquals('select * from `posts`', $result);
    }

    public function testGetSelectFromString_WithColumns_ReturnsColumns(){
        $result = QueryBuilderHelper::getSelectFromString(['posts'],['id','title','content']);
        $this->assertEquals('select `id`,`title`,`content` from `posts`', $result);
    }

    public function testGetSelectFromString_WithNoTable_Fail(){
        $fail = false;
        try{
            $result = QueryBuilderHelper::getSelectFromString(null,['id','title','content']);
        }
        catch (Exception $e){
            $fail = true;
        }
        $this->assertTrue($fail);
    }

    public function testGetSelectFromString_WithEmptyTableArray_Fail(){
        $fail = false;
        try{
            $result = QueryBuilderHelper::getSelectFromString([],['id','title','content']);
        }
        catch (Exception $e){
            $fail = true;
        }
        $this->assertTrue($fail);
    }

    // getWhereString

    public function testGetWhereString_ReturnsString(){
        $result = QueryBuilderHelper::getWhereString('id=10');
        $this->assertEquals(' where id=10', $result);
    }

    public function testGetWhereString_WithEmptyString_ReturnsStringEmpty(){
        $result = QueryBuilderHelper::getWhereString('  ');
        $this->assertEquals('', $result);
    }

    public function testGetWhereString_WithConditionOp_ReturnsCondition(){
        $result = QueryBuilderHelper::getWhereString(Condition::op('id','=',10));
        $this->assertEquals(' where `id`=10', $result);
    }

    public function testGetWhereString_WithConditionOpWithStringValue_ReturnsString(){
        $result = QueryBuilderHelper::getWhereString(Condition::op('id','=','10'));
        $this->assertEquals(' where `id`=\'10\'', $result);
    }

    // getOrderByString
    public function testGetOrderByString_WithOneSort_ReturnsSort(){
        $result = QueryBuilderHelper::getOrderByString(['id']);
        $this->assertEquals(' order by `id`', $result);
    }

    public function testGetOrderByString_WithMultiSort_ReturnsSort(){
        $result = QueryBuilderHelper::getOrderByString(['id', 'title']);
        $this->assertEquals(' order by `id`,`title`', $result);
    }

    public function testGetOrderByString_WithDirection_ReturnsDirection(){
        $result = QueryBuilderHelper::getOrderByString(['id ASC', 'title']);
        $this->assertEquals(' order by `id` ASC,`title`', $result);
    }

    public function testGetOrderByString_WithSortClass(){
        $result = QueryBuilderHelper::getOrderByString([Sort::asc('id')]);
        $this->assertEquals(' order by `id`', $result);
    }

    public function testGetOrderByString_WithMutlipleSortClass(){
        $result = QueryBuilderHelper::getOrderByString([Sort::asc('id'),Sort::asc('title')]);
        $this->assertEquals(' order by `id`,`title`', $result);
    }

    public function testGetOrderByString_WithMutlipleSortClassAndDirection(){
        $result = QueryBuilderHelper::getOrderByString([Sort::desc('id'),Sort::asc('title')]);
        $this->assertEquals(' order by `id` desc,`title`', $result);
    }

    // getOptionString

    public function testGetOptionString_WithNoOption_ReturnsEmptyString(){
        $result = QueryBuilderHelper::getOptionString([]);
        $this->assertEquals('', $result);
    }

    public function testGetOptionString_WithOneOption_ReturnsOption(){
        $result = QueryBuilderHelper::getOptionString(['DISTINCT']);
        $this->assertEquals('DISTINCT ', $result);
    }

    public function testGetOptionString_WithOptions_ReturnsOptions(){
        $result = QueryBuilderHelper::getOptionString(['DISTINCT', 'HIGH_PRIORITY']);
        $this->assertEquals('DISTINCT HIGH_PRIORITY ', $result);
    }

    // joinWithBackTick
    public function testJoinWithBacktick_WithOptions_ReturnsOptions(){
        $result = QueryBuilderHelper::joinWithBacktick(',', ['id', 'title']);
        $this->assertEquals('`id`,`title`', $result);
    }

    // getLimitString

    public function testGetLimitString(){
        $result = QueryBuilderHelper::getLimitString('5,10');
        $this->assertEquals(' limit 5,10', $result);
    }

    public function testGetLimitString_WithNoLimit_ReturnsemptyString(){
        $result = QueryBuilderHelper::getLimitString('');
        $this->assertEquals('', $result);
    }

    // explodeWithBackticks

    public function testExplodeWithBackticks(){
        $result = QueryBuilderHelper::explodeWithBackticks('posts.id');
        $this->assertEquals('`posts`.`id`', $result);
    }

    public function testExplodeWithBackticks_WithNo_WrapBackticks(){
        $result = QueryBuilderHelper::explodeWithBackticks('id');
        $this->assertEquals('`id`', $result);
    }

    // explodeOrderByString

    public function testExplodeOrderByString(){
        $result = QueryBuilderHelper::explodeOrderByString('id desc');
        $this->assertEquals('`id` desc', $result);
    }

    public function testExplodeOrderByString_WithMultiple(){
        $result = QueryBuilderHelper::explodeOrderByString('posts.id desc');
        $this->assertEquals('`posts`.`id` desc', $result);
    }

    public function testExplodeOrderByString_WithNoDirection(){
        $result = QueryBuilderHelper::explodeOrderByString('posts.id');
        $this->assertEquals('`posts`.`id`', $result);
    }

    // getInsertIntoString

    public function testGetInsertIntoString_WithValues_ReturnValues(){
        $result = QueryBuilderHelper::getInsertIntoString('posts',['id','title'],[1,'my title']);
        $this->assertEquals('insert into `posts` (`id`,`title`) values (1,\'my title\')', $result);
    }

    // getUpdateString

    public function testGetUpdateString(){
        $result = QueryBuilderHelper::getUpdateString('posts',['title' => 'new title', 'content' => 'new content' ],'id=10');
        $this->assertEquals('update `posts` set `title`=\'new title\',`content`=\'new content\' where id=10', $result);
    }

    public function testGetUpdateString_WithCondition(){
        $result = QueryBuilderHelper::getUpdateString('posts',['title' => 'new title', 'content' => 'new content' ],Condition::op('id','=',10));
        $this->assertEquals('update `posts` set `title`=\'new title\',`content`=\'new content\' where `id`=10', $result);
    }

    // delete from

    public function testGetDeleteFromString(){
        $result = QueryBuilderHelper::getDeleteFromString('posts','id=10');
        $this->assertEquals('delete from `posts` where id=10', $result);
    }

    public function testGetDeleteFromString_WithCondition(){
        $result = QueryBuilderHelper::getDeleteFromString('posts',Condition::op('id','=',10));
        $this->assertEquals('delete from `posts` where `id`=10', $result);
    }

    // getForeignKeyConditionString

    public function testGetForeignKeyConditionString(){
        $model = (object)[
            'id' => 1,
            'title' =>'Post 1',
            'content'=>'Content 1',
            'user_id'=>5
        ];
        $result = QueryBuilderHelper::getForeignKeyConditionString(['user_id'=>'id'],$model);
        $this->assertEquals('`id`=5', $result);
    }

    public function testGetForeignKeyConditionString_WithMultiKeys(){
        $model = (object)[
            'id' => 1,
            'title' =>'Post 1',
            'content'=>'Content 1',
            'user_id'=>5,
            'user_id2'=>10
        ];
        $result = QueryBuilderHelper::getForeignKeyConditionString(['user_id'=>'id','user_id2'=>'id2'],$model);
        $this->assertEquals('`id`=5 and `id2`=10', $result);
    }

    // is null
    public function testIsNull_WithAllNull_RetrunsTrue(){
        $model = (object)[
            'user_id' => null,
            'category_id'=> null
        ];
        $result = QueryBuilderHelper::isNull(['user_id','category_id'], $model);
        $this->assertTrue($result);
    }

    public function testIsNull_WithNoValue_ReturnsTrue(){
        $model =(object) [];
        $result = QueryBuilderHelper::isNull(['user_id','category_id'], $model);
        $this->assertTrue($result);
    }

    public function testIsNull_WitOne_RetrunsTrue(){
        $model = (object)[
            'user_id' => 1,
            'category_id'=> null
        ];
        $result = QueryBuilderHelper::isNull(['user_id','category_id'], $model);
        $this->assertTrue($result);
    }

    public function testIsNull_WithOneValueNotNull_ReturnsFalse(){
        $model = (object)[
            'user_id' => 1
        ];
        $result = QueryBuilderHelper::isNull(['user_id'], $model);
        $this->assertFalse($result);
    }

    public function testIsNull_WithNoNull_ReturnsFalse(){
        $model = (object)[
            'user_id' => 1,
            'category_id'=> 2
        ];
        $result = QueryBuilderHelper::isNull(['user_id','category_id'], $model);
        $this->assertFalse($result);
    }

    // test db column strategy
    public function testDdStrategy(){
        Db::setTableAndColumnStrategy('[',']');
        $result = QueryBuilderHelper::wrapWithBacktick('posts');
        $this->assertEquals('[posts]', $result);
    }

    public function testDdStrategy_WithSelect(){
        Db::setTableAndColumnStrategy('[',']');

        $result = QueryBuilderHelper::getSelectFromString(['posts'],['id','title','content'],['distinct']);
        $this->assertEquals('select distinct [id],[title],[content] from [posts]', $result);
    }

    public function testDdStrategy_WithWhere(){
        Db::setTableAndColumnStrategy('[',']');

        $result = QueryBuilderHelper::getWhereString(Condition::op('id',10));
        $this->assertEquals(' where [id]=10', $result);
    }

    public function testDdStrategy_WithOrderBy(){
        Db::setTableAndColumnStrategy('[',']');

        $result = QueryBuilderHelper::getOrderByString(['id desc','title']);
        $this->assertEquals(' order by [id] desc,[title]', $result);
    }

    public function testDbStrategy_WithInsert(){
        Db::setTableAndColumnStrategy('[',']');
        
        $result = QueryBuilderHelper::getInsertIntoString('posts',['id','title'],[1,'my title']);
        $this->assertEquals('insert into [posts] ([id],[title]) values (1,\'my title\')', $result);
    }
    
    public function testDbStrategy_WithUpdate(){
        Db::setTableAndColumnStrategy('[',']');
        
        $result = QueryBuilderHelper::getUpdateString('posts',['title' => 'new title', 'content' => 'new content' ],Condition::op('id','=',10));
        $this->assertEquals('update [posts] set [title]=\'new title\',[content]=\'new content\' where [id]=10', $result);
    }
    
    public function testDbStrategy_Delete(){
        Db::setTableAndColumnStrategy('[',']');
        
        $result = QueryBuilderHelper::getDeleteFromString('posts',Condition::op('id','=',10));
        $this->assertEquals('delete from [posts] where [id]=10', $result);
    }

}