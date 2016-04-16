<?php
/**
 * slince database library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Database\QueryCompiler;

use Slince\Database\Query;

interface QueryCompilerInterface
{
    /**
     * 编译query
     * @param Query $query
     * @return string
     */
    function compile(Query $query);
}