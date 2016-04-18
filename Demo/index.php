<?php
include __DIR__ . '/../vendor/autoload.php';
use Slince\Database\ConnectionManager;

$connectionManager = new ConnectionManager();
$connectionManager->register('default', [
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'port' => 3306,
    'dbname' => 'qimuyu',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8'
]);
$connection = $connectionManager->get('default');

$post = $connection->newQuery()->select('title')
    ->from('posts')
    ->where('id = 1')
    ->execute()
    ->fetch();
print_r($post);

