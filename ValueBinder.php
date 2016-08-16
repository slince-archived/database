<?php
/**
 * slince database library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Database;

use PDOStatement;

class ValueBinder
{
    /**
     * 参数
     * @var array
     */
    protected $parameters = [];

    /**
     * 参数类型
     * @var array
     */
    protected $parameterTypes = [];

    /**
     * 设置参数
     * @param $name
     * @param $value
     * @param null $type
     */
    function setParameter($name, $value, $type = null)
    {
        $this->parameters[$name] = $value;
        if (!is_null($type)) {
            $this->parameterTypes[$name] = $type;
        }
    }

    /**
     * 批量设置参数
     * @param array $parameters
     * @param array $parameterTypes
     */
    function setParameters(array $parameters, array $parameterTypes = [])
    {
        $this->parameters = $parameters;
        $this->parameterTypes = $parameterTypes;
    }

    /**
     * 获取参数
     * @param $name
     * @return mixed|null
     */
    function getParameter($name)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    /**
     * 获取指定的参数类型
     * @param $name
     * @return mixed|null
     */
    function getParameterType($name)
    {
        return isset($this->parameterTypes[$name]) ? $this->parameterTypes[$name] : null;
    }

    /**
     * 获取所有的参数
     * @return array
     */
    function getParameters()
    {
        return $this->parameters;
    }

    /**
     * 获取所有的参数类型
     * @return array
     */
    function getParameterTypes()
    {
        return $this->parameterTypes;
    }

    /**
     * 是否是空的
     * @return bool
     */
    function isEmpty()
    {
        return empty($this->parameters);
    }

    /**
     * 绑定参数到pdo statement
     * @param PDOStatement $statement
     */
    function attachTo(PDOStatement $statement)
    {
        foreach ($this->parameters as $name => $parameter) {
            $type = isset($this->parameterTypes[$name]) ? $this->parameterTypes[$name] : \PDO::PARAM_STR;
            $statement->bindValue($name, $parameter, $type);
        }
    }
}