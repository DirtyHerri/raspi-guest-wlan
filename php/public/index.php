<?php

require __DIR__ . '/../src/autoload.php';

use App\Application;

$config          = require __DIR__ . '/../src/config.php';
$config['DEBUG'] = file_exists(__DIR__ . '/../src/config_debug.php');

if ($config['DEBUG']) {
    $configDebug                = require __DIR__ . '/../src/config_debug.php';
    $config                     = array_replace($config, $configDebug);
    $debugServer                = [];
    $debugServer['REMOTE_ADDR'] = '192.168.255.240'; // ip from ../debug/arp
    $server                     = array_replace($_SERVER, $debugServer);
} else {
    $server = $_SERVER;
}

$app = new Application($config);
$app->setQuery($_GET);
$app->setPost($_POST);
$app->setServer($server);
$app->setLogger(new \App\Log\Logger($config['LOGS']));

session_start();
$app->run();