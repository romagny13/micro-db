<?php
namespace MicroPHP\Db;

use PDO;
use PDOStatement;

class Query
{
    protected $sql;
    protected  $dbh;
    protected $statement;

    public function __construct($sql,PDO $dbh, PDOStatement $statement)
    {
        $this->sql = $sql;
        $this->dbh = $dbh;
        $this->statement = $statement;
    }
}