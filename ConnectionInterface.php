<?php
namespace Slince\Database;

interface ConnectionInterface
{
    function newQuery();

    function insert();

    function update();

    function delete();
}