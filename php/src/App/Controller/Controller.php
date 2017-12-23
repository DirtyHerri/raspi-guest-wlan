<?php

namespace App\Controller;

use App\Application;
use App\Http\Request;
use App\Http\Response;

abstract class Controller
{

    protected $app;

    /**
     * AdminController constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param $view
     * @param array $params
     * @return Response
     */
    public function renderView($view, $params = [])
    {
        $resopnse = new Response();
        return $resopnse->render($this->app->config('VIEWS') . $view . '.php', $params);
    }

    /**
     * @return bool true, if new PIN was created
     * @throws \Exception
     */
    protected function createPin()
    {
        if (!file_exists($this->app->config('WLAN_PIN_FILE'))) {
            file_put_contents($this->app->config('WLAN_PIN_FILE'), random_int(100000, 999999) . "\n");
            return true;
        }
        return false;
    }

    protected function clearPin()
    {
        if (file_exists($this->app->config('WLAN_PIN_FILE'))) {
            return unlink($this->app->config('WLAN_PIN_FILE'));
        }
    }

    protected function getPin()
    {
        $pin = file_exists($this->app->config('WLAN_PIN_FILE'))
            ? file_get_contents($this->app->config('WLAN_PIN_FILE'))
            : '......';
        return trim($pin);
    }

    protected function getMac(Request $request)
    {
        return $request->getMac($this->app->config('ARP_TABLE'));
    }
}