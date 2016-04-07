<?php
/**
 * slince database library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Database;

use Slince\Database\Exception\InvalidArgumentException;

class ConnectionManager
{
    /**
     * 数据库配置
     * @var array
     */
    protected $configs = [];

    /**
     * 数据库连接
     * @var array
     */
    protected $connections = [];

    /**
     *  注册配置
     * @param $name
     * @param array $parameters
     * @return $this
     */
    public function register($name, array $parameters)
    {
        $this->configs[$name] = $parameters;
        return $this;
    }

    /**
     * 获取数据库连接
     * @param $name
     * @return Connection
     */
    public function get($name)
    {
        if (! isset($this->connections[$name])) {
            $this->connections[$name] = $this->createConnection($name);
        }
        return $this->connections[$name];
    }

    /**
     * 创建数据库连接
     * @param $name 配置名称
     * @return Connection
     */
    protected function createConnection($name)
    {
        if (! isset($this->configs[$name])) {
            throw new InvalidArgumentException(sprintf('Config "%s" is not defined', $name));
        }
        $config = $this->configs[$name];
        return new Connection($config);
    }
}