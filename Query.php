<?php
namespace Slince\Database;

use Slince\Database\Expression\CompositeExpression;
use Slince\Database\Expression\QueryExpression;

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
        'where' => null,
        'order' => [],
        'offset' => null,
        'limit' => null,
        'distinct' => [],
        'group' => [],
        'having' => null,
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
        if (!is_null($connection)) {
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

    function newExpr($conjunction = 'AND')
    {
        return new QueryExpression($conjunction);
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
        if (!is_array($fields)) {
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

    function where($expressions)
    {
        if (!$expressions instanceof QueryExpression) {
            if (!is_array($expressions)) {
                $expressions = func_get_args();
            }
            $expressions = $this->newExpr()->addMultiple($expressions);
        }
        $this->sqlParts['where'] = $expressions;
        return $this;
    }

    function andWhere($expressions)
    {
        $where = $this->getSqlPart('where');
        if (!$where instanceof QueryExpression) {
            $where = $this->newExpr(CompositeExpression::TYPE_AND);
        }
        $where = call_user_func_array([$where, 'andX'], func_get_args());
        $this->sqlParts['where'] = $where;
        return $this;
    }

    function orWhere($expressions)
    {
        $where = $this->getSqlPart('where');
        if (!$where instanceof QueryExpression) {
            $where = $this->newExpr(CompositeExpression::TYPE_OR);
        }
        $where = call_user_func_array([$where, 'orX'], func_get_args());
        $this->sqlParts['where'] = $where;
        return $this;
    }

    function group($field)
    {
        $group = is_array($field) ? $field : func_get_args();
        $this->sqlParts['group'] = $group;
        return $this;
    }

    function having($expressions)
    {
        if (!$expressions instanceof CompositeExpression) {
            if (!is_array($expressions)) {
                $expressions = func_get_args();
            }
            $expressions = new CompositeExpression(CompositeExpression::TYPE_AND, $expressions);
        }
        $this->sqlParts['having'] = $expressions;
        return $this;
    }


    function andHaving($expressions)
    {
        $expressions = is_array($expressions) ? $expressions : func_get_args();
        $having = $this->getSqlPart('having');
        if ($having instanceof CompositeExpression && $having->getType() == CompositeExpression::TYPE_AND) {
            $having->addMultiple($expressions);
        } else {
            array_unshift($expressions, $having);
            $having = new CompositeExpression(CompositeExpression::TYPE_AND, $expressions);
            $this->sqlParts['having'] = $having;
        }
        return $this;
    }

    function orHaving($expressions)
    {
        $expressions = is_array($expressions) ? $expressions : func_get_args();
        $having = $this->getSqlPart('having');
        if ($having instanceof CompositeExpression && $having->getType() == CompositeExpression::TYPE_OR) {
            $having->addMultiple($expressions);
        } else {
            array_unshift($expressions, $having);
            $having = new CompositeExpression(CompositeExpression::TYPE_OR, $expressions);
            $this->sqlParts['having'] = $having;
        }
        return $this;
    }

    function order($sort, $direction = null)
    {
        if (!is_array($sort)) {
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

    function limit($num)
    {
        $this->sqlParts['limit'] = $num;
        return $this;
    }

    function offset($num)
    {
        $this->sqlParts['offset'] = $num;
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