<?php

namespace App;

use App\Contracts\LoggerInterface;
use App\Exception\UnauthorizedException;
use App\Http\Request;
use App\Http\Response;

class Application
{
    protected $_config = [];
    protected $request;
    protected $logger;
    protected $routes;

    public function __construct(array $config, Request $request)
    {
        $this->_config = $config;
        $this->request = $request;
        $this->routes  = [];
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function config($key, $default = null)
    {
        return isset($this->_config[$key]) ? $this->_config[$key] : $default;
    }

    public function get($path, $controller)
    {
        $this->routes['GET'][$path] = $controller;
    }

    public function post($path, $controller)
    {
        $this->routes['POST'][$path] = $controller;
    }

    /**
     * @return Response
     */
    public function run()
    {
        try {
            $method = $this->request->getMethod();
            $path   = $this->request->getPath();
            if (isset($this->routes[$method]) && isset($this->routes[$method][$path])) {
                $controller = $this->routes[$method][$path];
                if ($controller instanceof \Closure) {
                    return $controller();
                } else if (is_string($controller)) {
                    $n  = "App\\Controller\\";
                    $c  = explode(':', $controller);
                    $nc = $n . $c[0];
                    $f  = $c[1];
                    $ci = new $nc($this);
                    return $ci->$f($this->request);
                }
            }
            //redirect 404, so devices detect our captive portal
            //without mod_rewrite, 'ErrorDocument 404 /' in apache virtual host would have done that
            $response = new Response();
            return $response->redirect('/');
        } catch (\Exception $ex) {
            if ($ex instanceof UnauthorizedException) {
                return new Response(403, 'Unauthorized');
            }
            if ($this->config('DEBUG')) {
                return new Response(500, $ex->getMessage() . '<br />' . $ex->getFile() . ' (' . $ex->getLine() . ')');
            }
            return new Response(500, 'Unknown error');
        }
    }

    public function log($text, $mac, $type)
    {
        $logger = $this->getLogger();
        if ($logger) {
            $macString = '';
            for ($i = 1; $i <= strlen($mac); $i++) {
                if ($i < strlen($mac) && $i % 2 == 0) $macString .= $mac[$i - 1] . ':';
                else $macString .= $mac[$i - 1];
            }

            $logger->write($macString . ' ' . $text, $type);
        }
    }

}