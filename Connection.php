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
        'mysql' => '\\Slince\\Database\\Driver\\MysqlDriver'
    ];

    function __construct(array $config)
    {
        if (empty($config['driver']) || ! isset(static::$supportedDrivers[$config['driver']])) {
            throw new InvalidArgumentException(sprintf('Driver "%s" is not supported', $config['driver']));
        }
        $this->driver = $this->createDriver($config['driver'], $config);
    }

    protected function createDriver($driver, array $config)
    {
        $driverClass = static::$supportedDrivers[$driver];
        return new $driverClass($config);
    }

    /**
     * 获取driver
     * @return DriverInterface
     */
    function getDriver()
    {
        return $this->driver;
    }

    function connect()
    {
        if (! $this->driver->connect()) {
            throw new RuntimeException('Unable to connect to the database');
        }
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

    function beginTransaction()
    {
        return $this->driver->beginTransaction();
    }

    function commit()
    {
        return $this->driver->commit();
    }

    function rollback()
    {
        return $this->driver->rollback();
    }

    function compileQuery(Query $query)
    {
        return $this->driver->compileQuery($query);
    }

    function execute($statement)
    {
        return $this->driver->execute($statement);
    }

    function query($statement)
    {
        return $this->driver->query($statement);
    }

    function run(Query $query)
    {
        $statement = $this->driver->prepare($this->compileQuery($query));
        $statement->execute();
        return $statement;
    }

    static function getSupportedDrivers()
    {
        return array_keys(static::$supportedDrivers);
    }
}