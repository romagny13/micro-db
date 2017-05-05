<?php

use \MicroPHP\Db\QueryBuilder;
use \MicroPHP\Db\Condition;
use MicroPHP\Db\Db;

class QueryBuilderTest  extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        Db::setTableAndColumnStrategy();
    }

    public function testSelect_WithColumns(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder->select('id','title','content')
            ->from('posts')
            ->build();

        $this->assertEquals('select `id`,`title`,`content` from `posts`', $queryString);
    }

    public function testSelect_WithNoColumns_ReturnsAll(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder->select()
            ->from('posts')
            ->build();

        $this->assertEquals('select * from `posts`', $queryString);
    }

    public function testSelect_WithMultiTables_ReturnsAllTables(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder->select()
            ->from('posts','users')
            ->build();

        $this->assertEquals('select * from `posts`,`users`', $queryString);
    }

    public function testSelect_WithColumnsAndTables(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder->select('id','title','content')
            ->from('posts','users')
            ->build();

        $this->assertEquals('select `id`,`title`,`content` from `posts`,`users`', $queryString);
    }


    public function testSelect_WithOption(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder->select('id','title','content')
            ->options('DISTINCT')
            ->from('posts')
            ->build();

        $this->assertEquals('select DISTINCT `id`,`title`,`content` from `posts`', $queryString);
    }

    public function testSelect_WithOptions(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder->select('id','title','content')
            ->options('DISTINCT','HIGH_PRIORITY')
            ->from('posts')
            ->build();

        $this->assertEquals('select DISTINCT HIGH_PRIORITY `id`,`title`,`content` from `posts`', $queryString);
    }

    public function testSelect_WithCondtionOp(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder->select('posts.id','users.id','title')
            ->from('posts','users')
            ->where(Condition::op('users.id','=',1))
            ->build();

        $this->assertEquals('select `posts`.`id`,`users`.`id`,`title` from `posts`,`users` where `users`.`id`=1', $queryString);
    }

    public function testSelect_WithWhereAndStringValue_ReturnsStringValue(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder->select('posts.id','users.id','title')
            ->from('posts','users')
            ->where(Condition::op('users.id','=','1'))
            ->build();
        $this->assertEquals('select `posts`.`id`,`users`.`id`,`title` from `posts`,`users` where `users`.`id`=\'1\'', $queryString);
    }

    public function testSelect_WithConditionBetween(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder->select('posts.id','users.id','title')
            ->from('posts','users')
            ->where(Condition::between('users.id',1,10))
            ->build();
        $this->assertEquals('select `posts`.`id`,`users`.`id`,`title` from `posts`,`users` where `users`.`id` between 1 and 10', $queryString);
    }

    public function testSelect_WithConditionIn(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder->select('posts.id','users.id','title')
            ->from('posts','users')
            ->where(Condition::in('users.id',['1','2','3']))
            ->build();
        $this->assertEquals('select `posts`.`id`,`users`.`id`,`title` from `posts`,`users` where `users`.`id` in (\'1\',\'2\',\'3\')', $queryString);
    }

    public function testSelect_WithConditionLike(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder->select('posts.id','users.id','title')
            ->from('posts','users')
            ->where(Condition::like('users.id','a%'))
            ->build();
        $this->assertEquals('select `posts`.`id`,`users`.`id`,`title` from `posts`,`users` where `users`.`id` like \'a%\'', $queryString);
    }

    public function testSelect_WithLimit(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder->select()
            ->from('posts')
            ->limit(10)
            ->build();

        $this->assertEquals('select * from `posts` limit 10', $queryString);
    }

    public function testSelect_WithLimitAndOffset(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder->select()
            ->from('posts')
            ->limit(5,10)
            ->build();

        $this->assertEquals('select * from `posts` limit 5,10', $queryString);
    }

    public function testSelect_WithOrderBy(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder->select()
            ->from('posts')
            ->orderBy('id','title')
            ->build();

        $this->assertEquals('select * from `posts` order by `id`,`title`', $queryString);
    }

    public function testSelect_WithOrderByAndExplode(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder->select()
            ->from('posts','users')
            ->orderBy('posts.id','title')
            ->build();

        $this->assertEquals('select * from `posts`,`users` order by `posts`.`id`,`title`', $queryString);
    }

    public function testSelectComplete(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder->select()
            ->options('DISTINCT')
            ->from('posts','users')
            ->orderBy('posts.id','title')
            ->limit(10)
            ->build();

        $this->assertEquals('select DISTINCT * from `posts`,`users` order by `posts`.`id`,`title` limit 10', $queryString);
    }

    // insert into

    public function testInsertInto_WithValues_ReturnsValues(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder
            ->insert_into('posts')
            ->columns('id','title')
            ->values(1,'my title')
            ->build();

        $this->assertEquals('insert into `posts` (`id`,`title`) values (1,\'my title\')', $queryString);
    }

    // update

    public function testUpdate(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder->update('posts')
            ->set(['title' => 'new title', 'content'=>'new content'])
            ->where('id=10')
            ->build();

        $this->assertEquals('update `posts` set `title`=\'new title\',`content`=\'new content\' where id=10', $queryString);
    }

    public function testUpdate_WithCondition(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder->update('posts')
            ->set(['title' => 'new title', 'content'=>'new content'])
            ->where(Condition::op('id','=',10))
            ->build();

        $this->assertEquals('update `posts` set `title`=\'new title\',`content`=\'new content\' where `id`=10', $queryString);
    }

    public function testDeleteFromString(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder->delete_from('posts')
            ->where('id=10')
            ->build();

        $this->assertEquals('delete from `posts` where id=10', $queryString);
    }

    public function testDeleteFromString_WithCondition(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder->delete_from('posts')
            ->where(Condition::op('id','=',10))
            ->build();

        $this->assertEquals('delete from `posts` where `id`=10', $queryString);
    }

    public function testConditionOp(){
        $queryBuilder = new QueryBuilder();
        $queryString = $queryBuilder->delete_from('posts')
            ->where(Condition::op('id',10))
            ->build();

        $this->assertEquals('delete from `posts` where `id`=10', $queryString);
    }
}