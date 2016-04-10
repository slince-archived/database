<?php
namespace Slince\Database\Tests;

use Slince\Database\Expression\QueryExpression;

class QueryTest extends DatabaseTestCase
{
    function getDriverName()
    {
        return 'mysql';
    }

    function testSimpleSelect()
    {
        $query = $this->connection->newQuery();
        $query->select('*')->from('posts');
        $sql = $this->connection->compileQuery($query);
        $this->assertEquals('SELECT * FROM posts', $sql);
    }

    function testSelectWithSimpleWhere()
    {
        $actualSql = 'SELECT * FROM posts AS p WHERE (id = 1) AND (status = 1)';
        $sql = $this->connection->compileQuery($this->connection->newQuery()->select('*')
            ->from('posts', 'p')
            ->where('id = 1', 'status = 1')
            ->andWhere(function(QueryExpression $qer) {
                return $qer->add('id = 2')->add('status = 4')->orX()->like('name', '%taosikai%');
            })
        );
        $this->assertEquals($actualSql, $sql);
        $sql = $this->connection->compileQuery($this->connection->newQuery()->select('*')
            ->from('posts', 'p')
            ->where(['id = 1', 'status = 1'])
        );
        $this->assertEquals($actualSql, $sql);
    }

    function testSelectWithAndWhere()
    {
        $actualSql = 'SELECT * FROM posts AS p WHERE (id = 1) AND (status = 1) AND (create_time > 1947056142)';
        $sql = $this->connection->compileQuery($this->connection->newQuery()->select('*')
            ->from('posts', 'p')
            ->where('id = 1')
            ->andWhere('status = 1', 'create_time > 1947056142')
        );
        $this->assertEquals($actualSql, $sql);
    }

    function testSelectWithOrWhere()
    {
        $actualSql = 'SELECT * FROM posts AS p WHERE ((id = 1) AND (status = 1)) OR (id = 2) OR (status = 2)';
        $sql = $this->connection->compileQuery($this->connection->newQuery()->select('*')
            ->from('posts', 'p')
            ->where('id = 1', 'status = 1')
            ->orWhere('id = 2', 'status = 2')
        );
        $this->assertEquals($actualSql, $sql);
    }

    function testSelectWithAndOrWhere()
    {
        $actualSql = 'SELECT * FROM posts AS p WHERE (((id = 1) AND (status = 1)) OR (id = 2) OR (status = 2)) AND (create_time > 1947056142)';
        $sql = $this->connection->compileQuery($this->connection->newQuery()->select('*')
            ->from('posts', 'p')
            ->where('id = 1', 'status = 1')
            ->orWhere('id = 2', 'status = 2')
            ->andWhere('create_time > 1947056142')
        );
        $this->assertEquals($actualSql, $sql);
    }
}