<?php
namespace Slince\Database\QueryCompiler;

use Slince\Database\Query;

class QueryCompiler implements QueryCompilerInterface
{

    function compile(Query $query)
    {
        $type = $query->getType();

    }

    protected function createSqlForSelect(array $sqlParts)
    {
        $statement = 'SELECT ' . implode(',', $sqlParts['select']) . ' From ';
        $normalized = '';
        foreach ($sqlParts['from'] as $from) {
            if (empty($from['alias'])) {
                $normalized .= $from['table'];
            } else {
                $normalized .= "{$from['table']} AS {$from['alias']}";
            }
        }
        $statement .= $normalized;
        $statement .= $sqlParts['where'] ? 'WHERE' . $this->processWhereParts($sqlParts['where']) : ''
            . $sqlParts['group'] ? 'GROUP BY' . $this->processGroupParts($sqlParts['group']) : ''
            . $sqlParts['having'] ? 'HAVING' . $this->processHavingParts() : ''
            . $sqlParts['order'] ? ' ORDER BY ' . $this->processOrderParts($sqlParts['order']) : '';
        if (! empty($sqlParts['limit'])) {
            $statement = $this->modifyLimitQuery($statement, $sqlParts['limit'], $sqlParts['offset']);
        }
        return $statement;
    }

    protected function processWhereParts($whereParts)
    {

    }

    function processHavingParts()
    {

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
}