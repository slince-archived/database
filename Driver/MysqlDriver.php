<?php
namespace Slince\Database\Driver;


use Slince\Database\QueryCompiler\MysqlQueryCompiler;

class MysqlDriver extends Driver
{
    protected function createDsn(array $config)
    {
        return "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
    }

    function createQueryCompiler()
    {
        return new MysqlQueryCompiler();
    }
}