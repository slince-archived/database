<?php
namespace Slince\Database;

use Slince\Database\Driver\DriverInterface;
use Slince\Database\Exception\InvalidArgumentException;
use Slince\Database\Exception\RuntimeException;

class Connection implements ConnectionInterface
{
    /**
     * @var DriverInterface
     */
    protected $driver;

    protected static $supportedDrivers = [
        'mysql' => '\\Slince\\Database\\Driver\\Mysql'
    ];

    function __construct(array $config)
    {
        if (empty($config['driver']) || ! isset(static::$supportedDrivers[$config['driver']])) {
            throw new InvalidArgumentException(sprintf('Driver "%s" is not supported', $config['driver']));
        }
        $this->driver = $this->getDriver($config['driver'], $config);
    }

    protected function getDriver($driver, array $config)
    {
        $driverClass = static::$supportedDrivers[$driver];
        return new $driverClass($config);
    }

    function connect()
    {
        if (! $this->driver->connect()) {
            throw new RuntimeException('Unable to connect to the database');
        }
    }

    static function getSupportedDrivers()
    {
         return array_keys(static::$supportedDrivers);
    }

    function newQuery()
    {
        return new Query();
    }

    function insert($table, $data)
    {
        $columns = array_keys($data);
        $this->newQuery()->insert($columns)
            ->into($table)
            ->values($data)
            ->execute();
    }

    function update($table, $data, $conditions = [])
    {
        $this->newQuery()->update($table)
            ->set($data)
            ->where($conditions)
            ->execute();
    }

    function delete($table, $conditions = [])
    {
        $this->newQuery()->delete()
            ->from($table)
            ->where($conditions)
            ->execute();
    }
}