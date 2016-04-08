<?php
namespace Slince\Database\Driver;

use PDO;
use Slince\Database\QueryCompiler\QueryCompiler;

abstract class Driver implements DriverInterface
{

    protected $config;
    
    /**
     * @var PDO
     */
    protected $pdo;

    function __construct(array $config)
    {
        $this->config = $config;
    }

    function connect()
    {
        if (is_null($this->pdo)) {
            $dsn = $this->createDsn($this->config);
            $this->pdo = new PDO($dsn, $this->config);
        }
        return true;
    }

    abstract protected function createDsn(array $config);

    function beginTransaction()
    {
        $this->connect();
        if ($this->pdo->inTransaction()) {
            return true;
        }
        $this->pdo->beginTransaction();
    }

    function commitTransaction()
    {
        $this->connect();
        if (!$this->pdo->inTransaction()) {
            return false;
        }
        return $this->pdo->commit();
    }

    function rollbackTransaction()
    {
        $this->connect();
        if (!$this->pdo->inTransaction()) {
            return false;
        }
        $this->pdo->rollBack();
    }

    function prepare($statement)
    {
        return $this->pdo->prepare($statement);
    }

    function execute($statement)
    {
        return $this->pdo->exec($statement);
    }

    function query($statement)
    {
        return $this->pdo->query($statement);
    }

    function compileQuery(Query $query)
    {
        return $this->getQueryCompiler()->compile($query);
    }

    function getQueryCompiler()
    {
        return new QueryCompiler();
    }
}