<?php

require __DIR__ . '/../src/autoload.php';

$debugConfig     = __DIR__ . '/../src/config_debug.php';
$config          = require __DIR__ . '/../src/config.php';
$config['DEBUG'] = file_exists($debugConfig);

if ($config['DEBUG']) {
    $configDebug                = require $debugConfig;
    $config                     = array_replace($config, $configDebug);
    $debugServer                = [];
//    $debugServer['REMOTE_ADDR'] = '192.168.255.240'; // fake ip from ../debug/arp
    $server                     = array_replace($_SERVER, $debugServer);
} else {
    $server = $_SERVER;
}

$request = new \App\Http\Request($_GET, $_POST, $server);
$app     = new \App\Application($config, $request);
$app->setLogger(new \App\Log\Logger($config['logs']));

$app->get('/', 'GuestController:index');
$app->get('/login', 'GuestController:login');
$app->post('/login', 'GuestController:login');
$app->post('/logout', 'GuestController:logout');
$app->get('/admin', 'AdminController:index');
$app->post('/reboot', 'AdminController:reboot');
$app->post('/halt', 'AdminController:halt');
$app->get('/list', 'AdminController:pinList');
$app->post('/list', 'AdminController:pinList');
$app->get('/pin', 'AdminController:pin');
$app->get('/status', 'AdminController:status');
$app->post('/toggle', 'AdminController:toggle');
$app->post('/clear_failed', 'AdminController:clearFailed');

session_start();
echo $app->run()->send();