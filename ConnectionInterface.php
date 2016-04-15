<?php
namespace Slince\Database;

interface ConnectionInterface
{
    function connect();
    
    function newQuery();

    function insert($table, array $data, array $types = []);

    function update($table, array $data, $conditions = [], array $types = []);

    function delete($table, $conditions = []);

    function beginTransaction();

    function commit();

    function rollback();

    function run(Query $query);
}