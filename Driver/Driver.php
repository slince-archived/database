<?php
namespace Slince\Database\Driver;

use PDO;
use Slince\Database\Query;
use Slince\Database\QueryCompiler\QueryCompiler;

abstract class Driver implements DriverInterface
{

    protected $config;
    
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var QueryCompiler
     */
    protected $queryCompiler;

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

    function beginTransaction()
    {
        $this->connect();
        if ($this->pdo->inTransaction()) {
            return true;
        }
        $this->pdo->beginTransaction();
    }

    function commit()
    {
        $this->connect();
        if (!$this->pdo->inTransaction()) {
            return false;
        }
        return $this->pdo->commit();
    }

    function rollback()
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
        if (! is_null($this->queryCompiler)) {
            return $this->queryCompiler;
        }
        return $this->queryCompiler = $this->createQueryCompiler();
    }
    abstract protected function createDsn(array $config);
    abstract protected function createQueryCompiler();
}