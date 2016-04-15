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
        try {
            $this->driver->connect();
        } catch (\Exception $e) {
            throw new RuntimeException('Unable to connect to the database');
        }
    }

    function newQuery()
    {
        return new Query();
    }

    function insert($table, array $data, array $types = [])
    {
        return $this->newQuery()->insert($table)
            ->values(array_combine(array_keys($data), array_fill(0, '?', count($data))))
            ->setParameters(array_values($data), $types)
            ->execute();
    }

    function update($table, array $data, $conditions = [], array $types = [])
    {
        return $this->newQuery()->update($table)
            ->set(array_combine(array_keys($data), array_fill(0, '?', count($data))))
            ->where($conditions)
            ->setParameters(array_values($data), $types)
            ->execute();
    }

    function delete($table, $conditions = [], array $types = [])
    {
        return $this->newQuery()->delete($table)
            ->where($conditions)
            ->execute();
    }

    function beginTransaction()
    {
        $this->connect();
        return $this->driver->beginTransaction();
    }

    function commit()
    {
        $this->connect();
        return $this->driver->commit();
    }

    function rollback()
    {
        $this->connect();
        return $this->driver->rollback();
    }

    function compileQuery(Query $query)
    {
        return $this->driver->compileQuery($query);
    }

    function execute($statement)
    {
        $this->connect();
        return $this->driver->execute($statement);
    }

    function query($statement)
    {
        $this->connect();
        return $this->driver->query($statement);
    }

    function prepare($statement)
    {
        $this->connect();
        return $this->driver->prepare($statement);
    }

    function run(Query $query)
    {
        $statement = $this->compileQuery($query);
        if ($query->getType() != Query::SELECT && $query->getValueBinder()->isEmpty()) {
            $result = $this->execute($statement);
        } else {
            $statement = $this->prepare($statement);
            $query->getValueBinder()->attachTo($statement);
            $statement->execute();
            if ($query->getType() != Query::SELECT) {
                $result = $statement->rowCount();
            } else {
                $result = $statement;
            }
        }
        return $result;
    }

    static function getSupportedDrivers()
    {
        return array_keys(static::$supportedDrivers);
    }

    protected function prepareBindings(array $bindings)
    {

    }
}