<?php
namespace Slince\Database\Tests;

use Slince\Database\ConnectionManager;

class ConnectionManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConnectionManager
     */
    protected $connectionManager;

    function setUp()
    {
        $this->connectionManager = new ConnectionManager();
    }

    function testRegister()
    {
        $key = 'default';
        $this->assertArrayNotHasKey($key, $this->connectionManager->getConfigs());
        $this->registerConfig($key);
        $this->assertArrayHasKey($key, $this->connectionManager->getConfigs());
        $this->assertNotEmpty($this->connectionManager->getConfig($key));
    }

    function testSingleton()
    {
        $key = 'default';
        $this->registerConfig($key);
        $connection = $this->connectionManager->get($key);
        $this->assertInstanceOf('\\Slince\\Database\\Connection', $connection);
        $this->assertEquals($connection, $this->connectionManager->get($key));
    }

    function registerConfig($key)
    {
        $this->connectionManager->register($key, [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'username' => 'root',
            'password' => '',
            'dbname' => 'qimuyu',
            'charset' => 'utf8'
        ]);
    }
}