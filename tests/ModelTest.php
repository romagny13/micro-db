<?php

use MicroPHP\Db\Db;
use \MicroPHP\Db\Condition;

class ModelTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        SetUp::setUpDb();
        Db::setTableAndColumnStrategy();
    }

    public function testGetModelName(){
        $model = new FakeUser();
        $result = $model->getModelName();
        $this->assertEquals(FakeUser::class,$result);
    }

    public function testGetTable(){
        $table ='users';
        $model = new FakeUser($table);
        $result = $model->getTable();
        $this->assertEquals($table,$result);
    }

    public function testGetColumns(){
        $table ='users';
        $columns = ['id','username'];
        $model = new FakeUser($table,$columns);
        $result = $model->getColumns();
        $this->assertSame($columns,$result);
    }

    // all

    public function testAll(){
        $result = FakePost::all();
        $this->assertTrue($result > 0);
        $this->assertTrue($result[0] instanceof FakePost);
        $this->assertEquals(1,$result[0]->id);
        $this->assertEquals('Post 1',$result[0]->title);
        $this->assertEquals('Content 1',$result[0]->content);
        $this->assertEquals(1,$result[0]->user_id);
        $this->assertEquals(2,$result[1]->id);
        $this->assertEquals('Post 2',$result[1]->title);
        $this->assertEquals('Content 2',$result[1]->content);
        $this->assertEquals(1,$result[1]->user_id);
    }

    public function testAll_WithLimit(){
        $result = FakePost::all(2);
        $this->assertEquals(2,count($result));
        $this->assertEquals(1,$result[0]->id);
        $this->assertEquals('Post 1',$result[0]->title);
        $this->assertEquals('Content 1',$result[0]->content);
        $this->assertEquals(1,$result[0]->user_id);
    }

    public function testAll_WithOffsetLimit(){
        $result = FakePost::all(1,2);
        $this->assertEquals(2,count($result));
        $this->assertEquals(2,$result[0]->id);
        $this->assertEquals('Post 2',$result[0]->title);
        $this->assertEquals('Content 2',$result[0]->content);
        $this->assertEquals(1,$result[0]->user_id);
    }

    // where

    public function testWhere_WithConditionString(){
        $result = FakePost::where('id>1');
        $this->assertTrue(count($result) > 0);
        $this->assertTrue($result[0] instanceof FakePost);
        $this->assertEquals(2,$result[0]->id);
        $this->assertEquals('Post 2',$result[0]->title);
        $this->assertEquals('Content 2',$result[0]->content);
        $this->assertEquals(1,$result[0]->user_id);
    }

    public function testWhere_WithCondition(){
        $result = FakePost::where(Condition::op('id','>',1));
        $this->assertTrue(count($result) > 0);
        $this->assertTrue($result[0] instanceof FakePost);
        $this->assertEquals(2,$result[0]->id);
        $this->assertEquals('Post 2',$result[0]->title);
        $this->assertEquals('Content 2',$result[0]->content);
        $this->assertEquals(1,$result[0]->user_id);
    }

    public function testWhere_WithConditionAndLimit(){
        $result = FakePost::where(Condition::op('id','>',1),1);
        $this->assertEquals(1,count($result));
        $this->assertTrue($result[0] instanceof FakePost);
        $this->assertEquals(2,$result[0]->id);
        $this->assertEquals('Post 2',$result[0]->title);
        $this->assertEquals('Content 2',$result[0]->content);
        $this->assertEquals(1,$result[0]->user_id);
    }

    public function testWhere_WithConditionAndOffsetLimit(){
        $result = FakePost::where(Condition::op('id','>',1),1,2);
        $this->assertEquals(2,count($result));
        $this->assertTrue($result[0] instanceof FakePost);
        $this->assertEquals(3,$result[0]->id);
        $this->assertEquals('Post 3',$result[0]->title);
        $this->assertEquals('Content 3',$result[0]->content);
        $this->assertEquals(1,$result[0]->user_id);
    }

    // find

    public function testFind(){
        $result = FakePost::find('id=2');
        $this->assertTrue($result instanceof FakePost);
        $this->assertEquals(2,$result->id);
        $this->assertEquals('Post 2',$result->title);
        $this->assertEquals('Content 2',$result->content);
        $this->assertEquals(1,$result->user_id);
    }

    public function testFind_WithCondition(){
        $result = FakePost::find(Condition::op('id',2));
        $this->assertTrue($result instanceof FakePost);
        $this->assertEquals(2,$result->id);
        $this->assertEquals('Post 2',$result->title);
        $this->assertEquals('Content 2',$result->content);
        $this->assertEquals(1,$result->user_id);
    }

    // add

    public function testCreate(){
        $result = FakePost::create([
            'title' => 'Post 6',
            'content' => 'Content 6',
            'user_id' => 2
        ]);

        $id = Db::getInstance(false)->lastInsertId();
        $this->assertTrue($result);
        $this->assertEquals(6,$id);
    }

    // udpate

    public function testUpdate(){
        $result = FakePost::update([
            'title' => 'Update Post 6',
            'content' => 'Updated Content 6',
            'user_id' => 2
        ],Condition::op('id',6));

        $this->assertTrue($result);
    }

    // delete

    public function testDelete(){
        $result = FakePost::delete(Condition::op('id',6));
        $this->assertTrue($result);
    }

    // query

    public function testQuery(){
        $result = FakePost::query('select * from posts where id=1')
            ->fetchObject(FakePost::class);
        $this->assertTrue($result instanceof FakePost);
        $this->assertEquals(1,$result->id);
        $this->assertEquals('Post 1',$result->title);
        $this->assertEquals('Content 1',$result->content);
        $this->assertEquals(1,$result->user_id);
    }

    // prepare

    public function testPrepare(){
        $result = FakePost::prepare('select * from posts where id=:id')
            ->setParam(':id',2)
            ->fetchObject(FakePost::class);
        $this->assertTrue($result instanceof FakePost);
        $this->assertEquals(2,$result->id);
        $this->assertEquals('Post 2',$result->title);
        $this->assertEquals('Content 2',$result->content);
        $this->assertEquals(1,$result->user_id);
    }

    // test relations

    public function testAll_WithRelation(){
        $result = FakePostWithRelation::all();
        $this->assertNotNull($result[0]->user);
        $user = $result[0]->user;
        $this->assertTrue($user instanceof FakeUser);
        $this->assertEquals(1,$user->id);
        $this->assertEquals('admin',$user->username);
    }

    public function testWhere_WithRelation(){
        $result = FakePostWithRelation::where(Condition::op('id',2));
        $this->assertNotNull($result[0]->user);
        $user = $result[0]->user;
        $this->assertTrue($user instanceof FakeUser);
        $this->assertEquals(1,$user->id);
        $this->assertEquals('admin',$user->username);
    }

    public function testFind_WithRelation(){
        $result = FakePostWithRelation::find(Condition::op('id',4));
        $this->assertNotNull($result->user);
        $user = $result->user;
        $this->assertTrue($user instanceof FakeUser);
        $this->assertEquals(2,$user->id);
        $this->assertEquals('marie',$user->username);
    }

    public function testFind_WithNullRelation_DontAddProperty(){
        $result = FakePostWithRelations::find(Condition::op('id',1));
        $this->assertNotNull($result->user);
        $this->assertNull($result->category);
    }

    public function testAll_WithRelations(){
        $result = FakePostWithRelations::all();

        $this->assertNotNull($result[0]->user);
        $user = $result[0]->user;
        $this->assertTrue($user instanceof FakeUser);
        $this->assertEquals(1,$user->id);
        $this->assertEquals('admin',$user->username);

        $this->assertNull($result[0]->category);
        $this->assertNotNull($result[1]->category);
        $cat = $result[1]->category;
        $this->assertTrue($cat instanceof FakeCategory);
        $this->assertEquals(1,$cat->id);
        $this->assertEquals('web',$cat->name);
    }

    public function testWhere_WithRelations(){
        $result = FakePostWithRelations::where(Condition::op('id',5));

        $this->assertNotNull($result[0]->user);

        $this->assertNotNull($result[0]->category);
        $cat = $result[0]->category;
        $this->assertTrue($cat instanceof FakeCategory);
        $this->assertEquals(2,$cat->id);
        $this->assertEquals('mobile',$cat->name);
    }

    public function testFind_WithRelations(){
        $result = FakePostWithRelations::find(Condition::op('id',2));

        // relation 1
        $this->assertNotNull($result->user);
        $user = $result->user;
        $this->assertTrue($user instanceof FakeUser);
        $this->assertEquals(1,$user->id);
        $this->assertEquals('admin',$user->username);
        // relation 2
        $this->assertNotNull($result->category);
        $cat = $result->category;
        $this->assertTrue($cat instanceof FakeCategory);
        $this->assertEquals(1,$cat->id);
        $this->assertEquals('web',$cat->name);
    }

}