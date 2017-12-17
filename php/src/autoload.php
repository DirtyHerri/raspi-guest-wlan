<?php

function __autoload($class)
{
    $parts = explode('\\', $class);
    require __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) . '.php';
}