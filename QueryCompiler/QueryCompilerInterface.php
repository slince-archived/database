<?php
namespace Slince\Database\QueryCompiler;

use Slince\Database\Query;

interface QueryCompilerInterface
{
    function compile(Query $query);
}