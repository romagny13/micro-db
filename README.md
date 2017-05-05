# Micro Db

* [MicroPHP](https://github.com/romagny13/micro-php)

## Installation

```
composer require romagny13/micro-db
```

## Usage

## Configure the database connection. 

Example:

```php
$settings = [
    'dsn' =>"mysql:host=localhost;dbname=blog",
    'username'=>'root',
    'password' =>''
];
Db::setConnectionStringSettings($settings['dsn'],$settings['username'],$settings['password']);
```

## Sql tables/columns Strategy

By default the columns are wrapped with back ticks

Example:


```sql
select `posts`.`id`,`title`,`content`,`users`.`id`,`users`.`username` from `posts`,`users` order by `title`
```

Change the strategy. Example:

```php
Db::setTableAndColumnStrategy('[',']');
```


```sql
select [posts].[id],[title],[content],[users].[id],[users].[username] from [posts],[users] order by [title]
```


## With the Query Builder:

* select
* insert_into
* update
* delete

Examples:

### Select

```php
$posts = Db::getInstance()
    ->select('id','title','content','user_id')
    ->from('posts')
    ->where(Condition::op('user_id',1))
    ->orderBy('title')
    ->limit(10)
    ->fetchAll();
```

Other example, fetch a class

```php
class Post { }

$posts = Db::getInstance()
    ->select('posts.id','title','content','user_id','users.id','users.username')
    ->from('posts','users')
    ->where('user_id=1')
    ->orderBy(Sort::desc('title'),'content desc')
    ->limit(2,10)
    ->fetchAll(Post::class);
```

Get the querystring :
```php
$queryString = Db::getInstance()->select('posts.id','title','content','user_id','users.id','users.username')
    ->from('posts','users')
    ->where('user_id=1')
    ->orderBy(Sort::desc('title'),'content desc')
    ->limit(2,10)
    ->build();

var_dump($queryString);
```

```sql
select `posts`.`id`,`title`,`content`,`user_id`,`users`.`id`,`users`.`username` from `posts`,`users` where user_id=1 order by `title` desc,`content` desc limit 2,10
```

## Insert

```php
$success = Db::getInstance()
    ->insert_into('posts')
    ->columns('title','content','user_id')
    ->execute(['my title','my content',1]);
```

Or

```php
$success = Db::getInstance()
    ->insert_into('posts')
    ->columns('title','content','user_id')
    ->values('my title','my content',1)
    ->execute();
```
and get the last inserted id

```php
$id = Db::getInstance()->lastInsertId();
```

## Update

```php
$success = Db::getInstance()
    ->update('posts')
    ->set([
        'title'=>'new title',
        'content' => 'new content'
    ])
    ->where(Condition::op('id',1))
    ->execute();
```

## Delete

```php
$success = Db::getInstance()
    ->delete_from('posts')
    ->where(Condition::op('id',1))
    ->execute();
```

### Condition helper

* op
* in
* between
* like

plus chaining conditions with:

* _and_
* _or_


## PDO

Simple query

```php
$posts = Db::getInstance()
    ->query('select * from posts')
    ->fetchAllWithClass(Post::class);
```

Query with params

```php
$posts = Db::getInstance()
    ->prepare('select * from posts where id=:id')
    ->setParam(':id',1)
    ->fetchObject(Post::class);
```

Other example

```php
$success = Db::getInstance()
    ->prepare('insert into posts (title,content,user_id) values (?,?,?)')
    ->execute(['My title', 'My content',2]);

$id = Db::getInstance()->lastInsertId();
```

Or with named params

```php
$success = Db::getInstance()
    ->prepare('insert into posts (title,content,user_id) values (:title,:content,:user_id)')
    ->setParam(':title','My title')
    ->setParam(':content','My content')
    ->setParam(':user_id',2)
    ->execute();

$id = Db::getInstance()->lastInsertId();
```

## Model

Create a model and define the db table. By default all columns will be filled ['*'].
```php
use \MicroPHP\Db\Model;

class PostModel extends Model
{
    public function __construct()
    {
        $this->table = 'posts';
    }
}
```

Define the columns to fill

```php
use \MicroPHP\Db\Model;

class PostModel extends Model
{
    public function __construct()
    {
        $this->table = 'posts';
        $this->columns = ['id','title','content','user_id'];
    }
}
```

### All

get all the records of the table

```php
$posts = PostModel::all();
```

With limit

Example: only 10 posts (maximum) will be returned
```php
$posts = PostModel::all(10);
```

With offset + limit

Example: only 10 posts (maximum) will be returned after 2 posts

```php
$posts = PostModel::all(2,10);
```

### Where

Allow to select the records to return (array)

```php
$posts = PostModel::where(Condition::op('user_id',1)->_or_(Condition::op('user_id',2)));
```

or with a string

```php
$posts = PostModel::where('user_id=1 or user_id=2');
```

With offset + limit

```php
$posts = PostModel::where('user_id=1 or user_id=2',2,10);
```

### Find

Returns only one item.

```php
$post = PostModel::find(Condition::op('id',1));
```

### Create

Example:

```php
$success = PostModel::create([
    'title' => 'My title',
    'content' => 'My content',
    'user_id' => 1
]);
```

### Update

Example:

```php
$success = PostModel::update([
    'title' => 'My new title',
    'content' => 'My new content'
],Condition::op('id',1));
```

### Delete

Example:

```php
$success = PostModel::delete(Condition::op('id',1));
```

### Query and prepare

Are shortcuts to Db functions.

### Relations

Add relations (0-1) or (1-1) to other models 

```php
class UserModel extends Model
{
    public function __construct()
    {
        $this->table = 'users';
    }
}

class CategoryModel extends Model
{
    public function __construct()
    {
        $this->table = 'categories';
    }
}

class PostModel extends Model
{
    public function __construct()
    {
        $this->table = 'posts';
        $this->columns = ['title','content'];

        // relations
        $this->addRelation('users',['user_id' => 'id'],UserModel::class, 'user');
        $this->addRelation('categories',['category_id' => 'id'],CategoryModel::class, 'category');
    }
}
```

addRelation parameters:

* foreign key table name
* foreign key => primary key pairs
* model to fill
* property name to add

User and category properties will be added to the post model.

Example:

```php
$post = PostModel::find(Condition::op('id',1));
```

