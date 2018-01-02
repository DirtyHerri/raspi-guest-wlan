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
        $params['title'] = $this->app->config('ssid')();
        $resopnse        = new Response();
        ob_start();
        extract($params);
        include $this->app->config('views') . $view . '.php';
        $params['content'] = ob_get_clean();
        return $resopnse->render($this->app->config('views') . 'layout.php', $params);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    protected function createPin()
    {
        if (!file_exists($this->app->config('wlan_pin_file'))) {
            file_put_contents($this->app->config('wlan_pin_file'), random_int(100000, 999999) . "\n");
            return true;
        }
        return false;
    }

    protected function clearPin()
    {
        return unlink($this->app->config('wlan_pin_file'));
    }

    protected function getPin()
    {
        $pin = file_exists($this->app->config('wlan_pin_file'))
            ? file_get_contents($this->app->config('wlan_pin_file'))
            : '......';
        return trim($pin);
    }

    protected function getPinList()
    {
        $list = [];
        if (file_exists($this->app->config('wlan_pin_list_file'))) {
            $list = file($this->app->config('wlan_pin_list_file'));
        }
        return $list;
    }

    protected function createPinList()
    {
        $list = $this->createPinArray();
        file_put_contents($this->app->config('wlan_pin_list_file'), implode("\n", $this->createPinArray()));
        return $list;
    }

    protected function createPinArray()
    {
        $list = [];
        for ($i = 1; $i <= 10; $i++) {
            $list[] = random_int(100000, 999999);
        }
        return $list;
    }

    protected function getMac(Request $request)
    {
        return $request->getMac($this->app->config('arp_table'));
    }
}