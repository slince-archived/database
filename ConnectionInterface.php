<?php
namespace Slince\Database;

interface ConnectionInterface
{
    function connect();
    
    function newQuery();

    function insert($table, $columns);

    function update($table, $data, $conditions = []);

    function delete($table, $conditions = []);

    function beginTransaction();

    function commit();

    function rollback();

    function run(Query $query);
}