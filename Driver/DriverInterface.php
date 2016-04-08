<?php
namespace Slince\Database\Driver;

use Slince\Database\Query;

interface DriverInterface
{
    function connect();

    function beginTransaction();

    function commitTransaction();

    function rollbackTransaction();

    /**
     * @param $statement
     * @return \PDOStatement
     */
    function prepare($statement);

    function execute($statement);

    function query($statement);

    function compileQuery(Query $query);
}