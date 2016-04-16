<?php
/**
 * slince database library
 * @author Tao <taosikai@yeah.net>
 */
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

    /**
     * 支持的数据库系统
     * @var array
     */
    protected static $supportedDrivers = [
        'mysql' => 'Slince\\Database\\Driver\\MysqlDriver'
    ];

    function __construct(array $config)
    {
        if (empty($config['driver']) || ! isset(static::$supportedDrivers[$config['driver']])) {
            throw new InvalidArgumentException(sprintf('Driver "%s" is not supported', $config['driver']));
        }
        $this->driver = $this->createDriver($config['driver'], $config);
    }

    /**
     * 创建driver
     * @param $driver
     * @param array $config
     * @return mixed
     */
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

    /**
     * 连接数据库
     * @return void
     * @throws RuntimeException
     */
    function connect()
    {
        try {
            $this->driver->connect();
        } catch (\Exception $e) {
            throw new RuntimeException('Unable to connect to the database');
        }
    }

    /**
     * 创建查询构造器
     * @return Query
     */
    function newQuery()
    {
        return new Query();
    }

    /**
     * 执行insert操作
     * @param $table
     * @param array $data
     * @param array $types
     * @return int|\PDOStatement
     */
    function insert($table, array $data, array $types = [])
    {
        return $this->newQuery()->insert($table)
            ->values($data)
            ->execute();
    }

    /**
     * 执行update操作
     * @param $table
     * @param array $data
     * @param array $conditions
     * @param array $types
     * @return int|\PDOStatement
     */
    function update($table, array $data, $conditions = [], array $types = [])
    {
        return $this->newQuery()->update($table)
            ->set($data)
            ->where($conditions)
            ->execute();
    }

    /**
     * 执行删除操作
     * @param $table
     * @param array $conditions
     * @param array $types
     * @return int|\PDOStatement
     */
    function delete($table, $conditions = [], array $types = [])
    {
        return $this->newQuery()->delete($table)
            ->where($conditions)
            ->execute();
    }

    /**
     * 开启事务
     * @return mixed
     */
    function beginTransaction()
    {
        $this->connect();
        return $this->driver->beginTransaction();
    }

    /**
     * 提交事务
     * @return mixed
     */
    function commit()
    {
        $this->connect();
        return $this->driver->commit();
    }

    /**
     * 回退事务
     * @return mixed
     */
    function rollback()
    {
        $this->connect();
        return $this->driver->rollback();
    }

    /**
     * 编译query
     * @param Query $query
     * @return mixed
     */
    function compileQuery(Query $query)
    {
        return $this->driver->compileQuery($query);
    }

    /**
     * 执行非查询的sql
     * @param $statement
     * @return int
     */
    function execute($statement)
    {
        $this->connect();
        return $this->driver->execute($statement);
    }

    /**
     * 执行查询sql
     * @param $statement
     * @return mixed
     */
    function query($statement)
    {
        $this->connect();
        return $this->driver->query($statement);
    }

    /**
     * 预处理sql
     * @param $statement
     * @return \PDOStatement
     */
    function prepare($statement)
    {
        $this->connect();
        return $this->driver->prepare($statement);
    }

    /**
     * 执行query
     * @param Query $query
     * @return int|\PDOStatement
     */
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

    /**
     * 获取支持的数据库系统
     * @return array
     */
    static function getSupportedDrivers()
    {
        return array_keys(static::$supportedDrivers);
    }
}