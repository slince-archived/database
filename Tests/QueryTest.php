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

    function testSelectWithExpression()
    {
        $actualSql = 'SELECT * FROM posts AS p WHERE ((id = 1) AND (status = 1)) OR (id = 2) OR (status = 2)';
        $query = $this->connection->newQuery();
        $sql = $this->connection->compileQuery($query->select('*')
            ->from('posts', 'p')
            ->where($query->newExpr()->eq('id', 1)->eq('status', 1)->orX()->eq('id', 2)->eq('status', 2))
        );
        $this->assertEquals($actualSql, $sql);
    }

    function testSelectWithOr()
    {
        $actualSql = 'SELECT * FROM posts AS p WHERE ((id = 1) AND (status = 1)) OR ((id = 2) AND (status = 2))';
        $query = $this->connection->newQuery();
        $sql = $this->connection->compileQuery($query->select('*')
            ->from('posts', 'p')
            ->where('id = 1', 'status = 1')
            ->orWhere($query->newExpr()->add('id = 2')->add('status = 2'))
        );
        $this->assertEquals($actualSql, $sql);
    }

    function testDeleteSimple()
    {
        $actualSql = 'DELETE FROM posts AS p WHERE id = 1';
        $sql = $this->connection->compileQuery($this->connection->newQuery()
            ->delete('posts', 'p')
            ->where('id = 1'));
        $this->assertEquals($actualSql, $sql);
    }

    function testUpdateSimple()
    {
        $actualSql = 'UPDATE posts AS p SET active = 1 WHERE id = 1';
        $sql = $this->connection->compileQuery($this->connection->newQuery()
            ->update('posts', 'p')
            ->set(['active' => 1])
            ->where('id = 1'));
        $this->assertEquals($actualSql, $sql);
    }


    function testInsertSimple()
    {
        $actualSql = 'INSERT INTO posts (name, active) VALUES (?, ?)';
        $sql = $this->connection->compileQuery($this->connection->newQuery()
            ->insert('posts')
            ->values(['name' => '?', 'active' => '?']));
        $this->assertEquals($actualSql, $sql);
    }
}