<?php
namespace Slince\Database\Tests;

use Slince\Database\Connection;
use Slince\Database\ConnectionManager;
use Slince\Database\Exception\RuntimeException;

abstract class DatabaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    protected $connection;

    protected $registerKey = 'default';

    protected $config;

    function setUp()
    {
        $this->config = @include __DIR__ . '/Config/config.php';
        if (empty($this->config)) {
            throw new RuntimeException("You should config database firstly");
        }
        $this->config['driver'] = $this->getDriverName();
        $connectionManager = new ConnectionManager();
        $connectionManager->register($this->registerKey, $this->config);
        $this->connection = $connectionManager->get($this->registerKey);
    }

    abstract protected function getDriverName();

    function testQuery()
    {
        $query = $this->connection->newQuery();
        $this->assertInstanceOf('\\Slince\\Database\\Query', $query);
    }
}