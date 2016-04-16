<?php
/**
 * slince database library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Database\Driver;

use Slince\Database\Query;

interface DriverInterface
{
    function connect();

    function beginTransaction();

    function commit();

    function rollback();

    /**
     * @param $statement
     * @return \PDOStatement
     */
    function prepare($statement);

    /**
     * 执行一条非查询
     * @param $statement
     * @return int
     */
    function execute($statement);

    function query($statement);

    function compileQuery(Query $query);
}