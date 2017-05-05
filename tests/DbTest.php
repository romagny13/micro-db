<?php

use \MicroPHP\Db\Db;

class FakePosts{

}

class DbTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        SetUp::setUpDb();
        Db::setTableAndColumnStrategy();
    }

    // insert

    public function testAdd(){
        $result = Db::getInstance()->query("insert into posts (title,content,user_id) values ('my title','my content',1)")
            ->execute();

        $this->assertTrue($result);
    }

    public function testAdd_ReturnsLastInserted(){
        $result = Db::getInstance()->query("insert into posts (title,content,user_id) values ('my title','my content',1)")
            ->execute();

        $this->assertTrue($result);
    }

    public function testAdd_WithQueryParams(){
        $result = Db::getInstance()->prepare("insert into posts (title,content,user_id) values (?,?,?)")
            ->execute(['my title','my content',1]);

        $this->assertTrue($result);
    }

    public function testAdd_WithQueryParamNames(){
        $result = Db::getInstance()->prepare("insert into posts (title,content,user_id) values (:title,:content,:user_id)")
            ->setParam(':title','my title')
            ->setParam(':content', 'my content')
            ->setParam(':user_id',1)
            ->execute();

        $this->assertTrue($result);
    }

    // update

    public function testUpdate(){
        $result = Db::getInstance()->query("update posts set title='updated title',content='updated content' where id=1")
            ->execute();

        $this->assertTrue($result);
    }

    public function testUpdate_WithQueryParams(){
        $result = Db::getInstance()->prepare("update posts set title='updated title',content='updated content' where id=?")
            ->execute([2]);

        $this->assertTrue($result);
    }

    public function testUpdate_WithParamNames(){
        $result = Db::getInstance()->prepare("update posts set title='updated title',content='updated content' where id=:id")
            ->setParam(':id',3)
            ->execute();

        $this->assertTrue($result);
    }

    // select

    public function testSelectAll(){
        $result = Db::getInstance()->query('select * from posts')
            ->fetchAll();

        $this->assertTrue(count($result)> 1);
    }

    public function testFetchClass(){
        $result = Db::getInstance()->query('select * from posts')
            ->fetchAllWithClass(FakePosts::class);

        $this->assertTrue(count($result)> 1);
    }

    public function testSelect_WithCondition(){
        $result = Db::getInstance()->query('select * from posts where user_id=1')
            ->fetchAll();

        $this->assertTrue(count($result)> 1);
    }

    public function testSelect_WithConditionAndParam(){
        $result = Db::getInstance()->prepare('select * from posts where user_id=:id')
            ->setParam(':id',1)
            ->fetchAll();

        $this->assertTrue(count($result)> 1);
    }

    public function testSelect_WithConditionAndParams(){
        $result = Db::getInstance()->prepare('select * from posts where user_id=:id1 or user_id=:id2')
            ->setParam(':id1',1)
            ->setParam(':id2',2)
            ->fetchAll();

        $this->assertTrue(count($result)> 1);
    }

    public function testSelect_WithRelation(){
//        $post = Db::getInstance()->query('select * from posts,users where id=?')
//            ->fetchObject(FakePosts::class,[1]);
//
//        
//        $user = Db::getInstance()->query('select * from posts,users where id=?')
//            ->fetchObject(FakePosts::class,[1]);
//        
//        $this->assertTrue(count($result)> 1);
    }


    // delete

    public function testDelete(){
        $result = Db::getInstance()->query("delete from posts where id=1")
            ->execute();

        $this->assertTrue($result);
    }

    public function testDelete_WithParamNames(){
        $result = Db::getInstance()->prepare("delete from posts where id=:id")
            ->setParam(':id',3)
            ->execute();

        $this->assertTrue($result);
    }

    // delete tables

//    public function testDeleteTables(){
//        Db::getInstance() = $this->getDb();
//        // all
//        $result = Db::getInstance()->query("drop table posts,users")
//            ->execute()
//            ->rowCount();
//
//        $this->assertEquals(2,$result);
//    }
}