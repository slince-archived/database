<?php
namespace Slince\Database\Driver;

interface DriverInterface
{
    function connect();

    function execute($sql);

    function query();
}