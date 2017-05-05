<?php
namespace MicroPHP\Db;


class QueryBuilder
{
    public function select(...$columns){
        if(count($columns) === 0){
            $columns = ['*'];
        }
        // select distinct col from table where cond orderby col
        return new SelectQuery($columns);
    }

    public function insert_into($table){
        // insert into table_name (colum_name, col2) values (?,?)
        return new InsertQuery($table);
    }

    public function update($table){
        // update table_name set column1 = value1, column2 = value2 where cond1 and ...
        return new UpdateQuery($table);
    }

    public function delete_from($table){
        // delete from table_name where id=10 and ...
        return new DeleteQuery($table);
    }
}