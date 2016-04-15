<?php
namespace Slince\Database;

use PDOStatement;

class ValueBinder
{
    protected $parameters = [];

    protected $parameterTypes = [];

    function setParameter($name, $value, $type = null)
    {
        $this->parameters[$name] = $value;
        if (!is_null($type)) {
            $this->parameterTypes[$name] = $type;
        }
    }

    function setParameters(array $parameters, array $parameterTypes = [])
    {
        $this->parameters = $parameters;
        $this->parameterTypes = $parameterTypes;
    }

    function getParameter($name)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    function getParameterType($name)
    {
        return isset($this->parameterTypes[$name]) ? $this->parameterTypes[$name] : null;
    }

    function getParameters()
    {
        return $this->parameters;
    }

    function getParameterTypes()
    {
        return $this->parameterTypes;
    }

    function isEmpty()
    {
        return empty($this->parameters);
    }
    function attachTo(PDOStatement $statement)
    {
        foreach ($this->parameters as $name => $parameter) {
            $type = isset($this->parameterTypes[$name]) ? $this->parameterTypes[$name] : null;
            $statement->bindValue($name, $parameter, $type);
        }
    }
}