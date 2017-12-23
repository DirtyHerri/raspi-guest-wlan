<?php

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;

class GuestController extends Controller
{

    public function index(Request $request)
    {
        if ($request->isPrivateSubnet($this->app->config('SUBNET_PRIVATE'))
            && false === $request->query('debug', false)) {
            $response = new Response();
            return $response->redirect('/admin');
        }

        // Redirect to show real address
        if (false == $this->app->config('DEBUG')
            && $request->server("SERVER_NAME") != $request->server("SERVER_ADDR")) {
            $response = new Response();
            return $response->redirect("http://" . $request->server("SERVER_ADDR") . "/");
        }

        $mac = $this->getMac($request);

        if (!empty($mac) && file_exists($this->app->config('WLANGUEST_DIR') . $mac)) {
            return $this->renderView('logged_in');
        }

        $this->app->log("{$request->server('REMOTE_ADDR')}", $this->getMac($request), 'ACCESS');
        $this->createPin();
        return $this->renderView('index');
    }

    public function login(Request $request)
    {
        if ($request->post('acceptagb') == 'yes') {
            $pin = $this->getPin();
            $mac = $this->getMac($request);
            $ip  = $request->server('REMOTE_ADDR');
            if ($request->post('wlanpin') === $pin && !empty($mac)) {
                $this->app->log("{$request->server('REMOTE_ADDR')}", $mac, 'AUTH');
                file_put_contents($this->app->config('WLANGUEST_DIR') . $mac, $ip . "\n");
                $this->clearPin();
                $response = new Response();
                return $response->redirect('/');
            }
            return $this->renderView('index', ['error' => 'PIN ungültig']);
        }
        $response = new Response();
        return $response->redirect('/');
    }

    public function logout(Request $request)
    {
        if ($request->post('logout') === 'yes') {
            $mac = $this->getMac($request);
            if (file_exists($this->app->config('WLANGUEST_DIR') . $mac)) {
                unlink($this->app->config('WLANGUEST_DIR') . $mac);
            }
            $this->app->log("{$request->server('REMOTE_ADDR')}", $mac, 'LOGOUT');
        }
        $response = new Response();
        return $response->redirect('/');
    }

}