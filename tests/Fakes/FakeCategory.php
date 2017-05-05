<?php


class FakeCategory extends \MicroPHP\Db\Model
{
    public function __construct()
    {
        $this->table = 'categories';
    }
}