<?php
namespace Slince\Database\QueryCompiler;

use Slince\Database\Query;

class QueryCompiler implements QueryCompilerInterface
{

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

    protected function processWhereParts($whereParts)
    {
        return strval($whereParts);
    }

    protected function processGroupParts($groupParts)
    {
        return implode(',', $groupParts);
    }

    protected function processHavingParts($havingParts)
    {
        return strval($havingParts);
    }

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

    protected function modifyLimitQuery($statement, $limit, $offset = null)
    {
        if (is_null($offset)) {
            $statement .= " LIMIT {$limit}";
        } else {
            $statement = " LIMIT {$offset}, {$limit}";
        }
        return $statement;
    }

    function createSqlForInsert(array $sqlParts)
    {
        return "INSERT INTO {$sqlParts['insert']} ("
            . implode(', ', array_keys($sqlParts['values']))
            . ') VALUES (' . implode(', ', $sqlParts['values']) . ')';
    }

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

    function createSqlForDelete(array $sqlParts)
    {
        return 'DELETE FROM '
            . $this->processTables([$sqlParts['delete']])
            . ($sqlParts['where'] ? ' WHERE ' . (string)$sqlParts['where'] : '');
    }

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