<?php

require __DIR__ . '/../src/autoload.php';

$config          = require __DIR__ . '/../src/config.php';
$config['DEBUG'] = file_exists(__DIR__ . '/../src/config_debug.php');

if ($config['DEBUG']) {
    $configDebug                = require __DIR__ . '/../src/config_debug.php';
    $config                     = array_replace($config, $configDebug);
    $debugServer                = [];
//    $debugServer['REMOTE_ADDR'] = '192.168.255.240'; // ip from ../debug/arp
    $server                     = array_replace($_SERVER, $debugServer);
} else {
    $server = $_SERVER;
}

$request = new \App\Http\Request($_GET, $_POST, $server);
$app     = new \App\Application($config, $request);
$app->setLogger(new \App\Log\Logger($config['LOGS']));

$app->get('/', 'GuestController:index');
$app->get('/login', 'GuestController:login');
$app->post('/login', 'GuestController:login');
$app->post('/logout', 'GuestController:logout');
$app->get('/admin', 'AdminController:index');
$app->get('/pin', 'AdminController:pin');
$app->get('/status', 'AdminController:status');
$app->post('/toggle', 'AdminController:toggle');

session_start();
$app->run()->send();