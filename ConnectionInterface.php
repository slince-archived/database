<?php
namespace Slince\Database;

interface ConnectionInterface
{
    function newQuery();

    function insert($table, $columns);

    function update($table, $data, $conditions = []);

    function delete($table, $conditions = []);

    function begin();

    function commit();

    function connect();

    function rollnack();

    function run();

}