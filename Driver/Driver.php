<?php
namespace Slince\Database\Driver;

use Slince\Database\QueryCompiler\QueryCompiler;

class Driver implements DriverInterface
{

    function connect()
    {
    }

    function query()
    {

    }
    function execute($sql)
    {

    }

    function compileQuery(Query $query)
    {
        return $this->getQueryCompiler()->compile($query);
    }

    function getQueryCompiler()
    {
        return new QueryCompiler();
    }
}