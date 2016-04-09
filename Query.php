<?php
namespace Slince\Database;

use Slince\Database\Expression\CompositeExpression;

class Query
{
    const SELECT = 1;
    const DELETE = 2;
    const UPDATE = 3;
    const INSERT = 4;

    protected $type;

    protected $sqlParts = [
        'select' => [],
        'from' => [],
        'where' => [],
        'order' => [],
        'offset' => null,
        'limit' => null,
        'distinct' => [],
        'group' => [],
        'having' => [],
        'insert' => [],
        'into' => null,
        'values' => [],
        'update' => [],
        'set' => [],
    ];
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

    function getType()
    {
        return $this->type;
    }

    function getSqlParts()
    {
        return $this->sqlParts;
    }

    function getSqlPart($name)
    {
        return $this->sqlParts[$name] ?: null;
    }

    public function insert($table)
    {
        $this->type = self::INSERT;
        $this->sqlParts['insert'] = $table;
        return $this;
    }

    function values($data)
    {
        $this->sqlParts['values'] = $data;
        return $this;
    }

    function update($table, $alias = null)
    {
        $this->type = self::UPDATE;
        $this->sqlParts['update'] = [
            'table' => $table,
            'alias' => $alias
        ];
        return $this;
    }

    function set($data)
    {
        $this->sqlParts['set'] = $data;
        return $this;
    }

    function delete()
    {
        $this->type = self::DELETE;
        return $this;
    }

    function select($fields = null)
    {
        $this->type = self::SELECT;
        if (! is_array($fields)) {
            $fields = func_get_args();
        }
        $this->sqlParts['select'] = $fields;
        return $this;
    }

    function from($table, $alias = null)
    {
        $this->sqlParts['from'][] = [
            'table' => $table,
            'alias' => $alias
        ];
        return $this;
    }

    function where($expression)
    {
        if (! $expression instanceof CompositeExpression) {
            $condition = $expression;
            if (! is_array($condition)) {
                $condition = func_get_args();
            }
            $expression = new CompositeExpression(CompositeExpression::TYPE_AND, $condition);
        }
        $this->sqlParts['where'] = $expression;
        return $this;
    }

    function andWhere($expression)
    {
        $where = $this->getSqlPart('where');
        if ($where instanceof CompositeExpression) {
            $where->addMultiple($conditions);
        }
    }
    function orWhere($conditions)
    {
        $where = $this->getSqlPart('where');
        if ($where instanceof CompositeExpression) {
            $where->addMultiple($conditions);
        }
    }

    function offset($num)
    {
        $this->sqlParts['offset'] = $num;
        return $this;
    }
    function limit($num)
    {
        $this->sqlParts['limit'] = $num;
        return $this;
    }

    function order($sort, $direction = null)
    {
        if (! is_array($sort)) {
            $order[] = [
                'sort' => $sort,
                'direction' => $direction
            ];
        } else {
            $order = $sort;
        }
        $this->sqlParts['order'] = $order;
        return $this;
    }

    function group($field)
    {
        $group = $field;
        if (! is_array($field)) {
            $group = [$field];
        }
        $this->sqlParts['group'] = $group;
        return $this;
    }

    function having($conditions)
    {
        $this->sqlParts['having'] = $conditions;
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