<?php

namespace MicroPHP\Db;

use \PDO;


class Db
{
    protected $dbh;
    protected $queryBuilder;

    private static $instance;
    public static function getInstance($connect=true){
        if(!isset(self::$instance)){
            self::$instance = new Db();
            if($connect){
                if(!isset(self::$settings)) { throw new \Exception('No settings. Use setSettings to define db settings'); }
                self::$instance->connect(self::$settings['dsn'],self::$settings['username'],self::$settings['password'],self::$settings['options']);
            }
        }
        return self::$instance;
    }

    private static $settings;
    public static function getSettings(){
        return self::$settings;
    }

    public static function setConnectionStringSettings($dsn,$username,$password,array $options=[]){
        self::$settings['dsn'] = $dsn;
        self::$settings['username'] = $username;
        self::$settings['password'] = $password;
        self::$settings['options'] = $options;
    }

    public static function setTableAndColumnStrategy($start='`', $end='`'){
        self::$settings['start'] = $start;
        self::$settings['end'] = $end;
    }

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
    }

    /**
     * @param $dsn (example:"mysql:host=localhost;dbname=mydb") http://php.net/manual/fr/pdo.construct.php
     * @param $username (example:'root')
     * @param $password (example: '' with no password)
     * @param array $options
     * @return $this
     */
    public function connect($dsn,$username,$password,$options=[]){
        // $dsn example => "mysql:host=".$host.";dbname=" .$db
        $this->dbh = new PDO($dsn,$username, $password, $options);
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        return $this;
    }
    
    public function query($sql){
        $statement = $this->dbh->query($sql);
        return new SimpleQuery($sql, $this->dbh, $statement);
    }

    public function prepare($sql){
        $statement = $this->dbh->prepare($sql);
        return new PreparedQuery($sql, $this->dbh, $statement);
    }

    public function lastInsertId(){
        return $this->dbh->lastInsertId();
    }


    public function select(...$columns){
        if(count($columns) === 0){
            $columns = ['*'];
        }

        // select distinct col from table where cond orderby col
        return $this->queryBuilder->select(...$columns);
    }

    public function insert_into($table){
        // insert into table_name (colum_name, col2) values (?,?)
        return $this->queryBuilder->insert_into($table);
    }

    public function update($table){
        // update table_name set column1 = value1, column2 = value2 where cond1 and ...
        return $this->queryBuilder->update($table);
    }

    public function delete_from($table){
        // delete from table_name where id=10 and ...
        return $this->queryBuilder->delete_from($table);
    }
}
