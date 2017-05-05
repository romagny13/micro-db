<?php


class FakePost extends \MicroPHP\Db\Model
{
    public function __construct()
    {
        $this->table = 'posts';
        $this->columns = ['id','title','content','user_id'];
    }
}


class FakePostWithRelation extends \MicroPHP\Db\Model
{
    public function __construct()
    {
        $this->table = 'posts';
        $this->columns = ['id','title','content','user_id'];

        // fk 1 or more columns => table (example: user_id => users)
        $this->addRelation('users',['user_id' => 'id'],FakeUser::class,'user');
    }
}

class FakePostWithRelations extends \MicroPHP\Db\Model
{
    public function __construct()
    {
        $this->table = 'posts';
        $this->columns = ['id','title','content','user_id'];

        // 1 - 1
        $this->addRelation('users',['user_id' => 'id'],FakeUser::class, 'user');

        // 0 - 1
        $this->addRelation('categories',['category_id' => 'id'],FakeCategory::class, 'category');
    }
}

