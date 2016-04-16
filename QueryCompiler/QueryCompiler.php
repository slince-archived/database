<?php
/**
 * slince database library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Database\QueryCompiler;

use Slince\Database\Query;

class QueryCompiler implements QueryCompilerInterface
{

    /**
     * 编译query
     * @param Query $query
     * @return string
     */
    function compile(Query $query)
    {
        $type = $query->getType();
        $statement = '';
        switch ($type) {
            case Query::SELECT:
                $statement = $this->createSqlForSelect($query->getSqlParts());
                break;
            case Query::INSERT:
                $statement = $this->createSqlForInsert($query->getSqlParts());
                break;
            case Query::UPDATE:
                $statement = $this->createSqlForUpdate($query->getSqlParts());
                break;
            case Query::DELETE:
                $statement = $this->createSqlForDelete($query->getSqlParts());
                break;
        }
        return $statement;
    }

    /**
     * 编译查询sql
     * @param array $sqlParts
     * @return string
     */
    protected function createSqlForSelect(array $sqlParts)
    {
        $statement = 'SELECT ' . implode(',', $sqlParts['select']) . ' FROM ';
        $statement .= $this->processTables($sqlParts['from'])
            . ($sqlParts['where'] ? ' WHERE ' . $this->processWhereParts($sqlParts['where']) : '')
            . ($sqlParts['group'] ? ' GROUP BY ' . $this->processGroupParts($sqlParts['group']) : '')
            . ($sqlParts['having'] ? ' HAVING ' . $this->processHavingParts() : '')
            . ($sqlParts['order'] ? ' ORDER BY ' . $this->processOrderParts($sqlParts['order']) : '');
        if (! empty($sqlParts['limit'])) {
            $statement = $this->modifyLimitQuery($statement, $sqlParts['limit'], $sqlParts['offset']);
        }
        return $statement;
    }

    /**
     * 处理where条件
     * @param $whereParts
     * @return string
     */
    protected function processWhereParts($whereParts)
    {
        return strval($whereParts);
    }

    /**
     * 处理group
     * @param $groupParts
     * @return string
     */
    protected function processGroupParts($groupParts)
    {
        return implode(',', $groupParts);
    }

    /**
     * 处理having
     * @param $havingParts
     * @return string
     */
    protected function processHavingParts($havingParts)
    {
        return strval($havingParts);
    }

    /**
     * 处理order
     * @param $orderParts
     * @return string
     */
    protected function processOrderParts($orderParts)
    {
        $statementParts = array_map(function($order){
            if (empty($order['direction'])) {
                $order['direction'] = 'ASC';
            }
            return "{$order['sort']} {$order['direction']}";
        }, $orderParts);
        return implode(',', $statementParts);
    }

    /**
     * 修改limit query
     * @param $statement
     * @param $limit
     * @param null $offset
     * @return string
     */
    protected function modifyLimitQuery($statement, $limit, $offset = null)
    {
        if (is_null($offset)) {
            $statement .= " LIMIT {$limit}";
        } else {
            $statement = " LIMIT {$offset}, {$limit}";
        }
        return $statement;
    }

    /**
     * 编译insert query
     * @param array $sqlParts
     * @return string
     */
    function createSqlForInsert(array $sqlParts)
    {
        return "INSERT INTO {$sqlParts['insert']} ("
            . implode(', ', array_keys($sqlParts['values']))
            . ') VALUES (' . implode(', ', $sqlParts['values']) . ')';
    }

    /**
     * 编译更新query
     * @param array $sqlParts
     * @return string
     */
    function createSqlForUpdate(array $sqlParts)
    {
        $statement = '';
        foreach ($sqlParts['set'] as $field => $value) {
            $statement .= "{$field} = {$value}";
        }
        return 'UPDATE ' . $this->processTables([$sqlParts['update']])
            . " SET {$statement}"
            . ($sqlParts['where'] ? ' WHERE ' . (string)$sqlParts['where'] : '');
    }

    /**
     * 编译delete query
     * @param array $sqlParts
     * @return string
     */
    function createSqlForDelete(array $sqlParts)
    {
        return 'DELETE FROM '
            . $this->processTables([$sqlParts['delete']])
            . ($sqlParts['where'] ? ' WHERE ' . (string)$sqlParts['where'] : '');
    }

    /**
     * 处理表名
     * @param $tables
     * @return mixed
     */
    protected function processTables($tables)
    {
        $statements = array_map(function($table){
            if (empty($table['alias'])) {
                $normalized = $table['table'];
            } else {
                $normalized = "{$table['table']} AS {$table['alias']}";
            }
            return $normalized;
        }, $tables);
        return implode(',', $statements);
    }
}