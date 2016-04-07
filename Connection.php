<?php
namespace Slince\Database;

use Slince\Database\Driver\DriverInterface;

class Connection implements ConnectionInterface
{
    /**
     * @var DriverInterface
     */
    protected $driver;

    function connect()
    {
        try {
            $this->driver->connect()
        }
    }
}