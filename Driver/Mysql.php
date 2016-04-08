<?php
namespace Slince\Database\Driver;


class Mysql extends Driver
{
    protected function createDsn(array $config)
    {
        return "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
    }
}