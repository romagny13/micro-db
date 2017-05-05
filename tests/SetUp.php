<?php


use MicroPHP\Db\Db;

class SetUp
{

    public static function setUpDb(){
        //  exec("mysql -u'root' --password='' < ".__DIR__."/db.sql");

        $settings = [
            'dsn' =>"mysql:host=localhost;dbname=db_test",
            'username'=>'root',
            'password' =>''
        ];
        Db::setConnectionStringSettings($settings['dsn'],$settings['username'],$settings['password']);

        // create db
        $dbh = new PDO($settings['dsn'], $settings['username'], $settings['password']);
        $sql = file_get_contents(__DIR__ . '/db.sql');
        $dbh->exec($sql);
    }
}