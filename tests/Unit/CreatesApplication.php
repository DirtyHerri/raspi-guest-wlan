<?php

namespace Tests\Unit;


use App\Application;

trait CreatesApplication
{

    /**
     * @var Application
     */
    protected $app;
    protected $request;

    public function createApp($query = [], $post = [], $server = [])
    {
        $_query        = array_merge($query, []);
        $_post         = array_merge($post, []);
        $_server       = array_merge($server, []);
        $config        = require __DIR__ . '/config.php';
        $this->request = new \App\Http\Request($_query, $_post, $_server);
        $this->app     = new \App\Application($config, $this->request);
    }

}