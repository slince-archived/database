<?php
namespace Slince\Database;

class Query
{
    /**
     * @var Connection
     */
    protected $connection;

    function __construct(Connection $connection = null)
    {
        if (! is_null($connection)) {
            $this->setConnection($connection);
        }
    }

    function setConnection(Connection $connection)
    {
        $this->connection = $connection;
    }

    function getConnection()
    {
        return $this->connection;
    }

    public function insert(array $columns, array $types = [])
    {
        return $this;
    }

    function into($table)
    {
        return $this;
    }

    function values($data)
    {
        return $this;
    }

    function update($table)
    {
        return $this;
    }

    function set($data)
    {
        return $this;
    }

    function delete()
    {
        return $this;
    }

    function select(array $fields = [])
    {
        return $this;
    }

    function from($table)
    {
        return $this;
    }

    function where($conditions)
    {
        return $this;
    }

    function offset($num)
    {
        return $this;
    }
    function limit($num)
    {
        return $this;
    }

    function order()
    {
        return $this;
    }

    function group($fields)
    {
        return $this;
    }

    function having()
    {
        return $this;
    }

    function toSql()
    {
        return $this->getConnection()->compileQuery($this);
    }

    function execute()
    {
        return $this->connection->run($this);
    }
}