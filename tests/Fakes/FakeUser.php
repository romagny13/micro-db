<?php

use MicroPHP\Db\Model;

class FakeUser extends Model
{
    public function __construct($table = null, $columns = [])
    {
        $this->table = $table;
        $this->columns = $columns;
    }
}