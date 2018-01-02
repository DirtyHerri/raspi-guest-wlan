<?php

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;

class GuestController extends Controller
{

    public function index(Request $request)
    {
        if ($request->isPrivateSubnet($this->app->config('subnet_private'))
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

        if (!empty($mac) && file_exists($this->app->config('wlan_guest_dir') . $mac)) {
            return $this->renderView('logged_in');
        }

        $this->app->log("{$request->server('REMOTE_ADDR')}", $this->getMac($request), 'ACCESS');
        $this->createPin();
        return $this->renderView('index');
    }

    public function login(Request $request)
    {
        if ($request->post('acceptagb') == 'yes') {
            $mac     = $this->getMac($request);
            $ip      = $request->server('REMOTE_ADDR');
            $auth    = false;
            if (file_exists($this->app->config('fail_log'))) {
                $failLog = file($this->app->config('fail_log'), FILE_IGNORE_NEW_LINES);
                $counts  = array_count_values($failLog);

                if (isset($counts[$mac]) && $counts[$mac] > 5) {
                    return $this->renderView('index', ['error' => '403']);
                }
            }

            if (file_exists($this->app->config('wlan_pin_list_file'))) {
                $fp = fopen($this->app->config('wlan_pin_list_file'), "r+");
                if (flock($fp, LOCK_EX | LOCK_NB)) {
                    $list = fread($fp, filesize($this->app->config('wlan_pin_list_file')));
                    $list = explode("\n", $list);
                    if (in_array($request->post('wlanpin'), $list)) {
                        $auth = true;
                        if (($key = array_search($request->post('wlanpin'), $list)) !== false) {
                            unset($list[$key]);
                        }
                        ftruncate($fp, 0);
                        fseek($fp, 0);
                        $listOut = implode("\n", $list);
                        fwrite($fp, $listOut);
                    }
                    flock($fp, LOCK_UN);
                    fclose($fp);
                } else {
                    fclose($fp);
                    return $this->renderView('index', ['error' => 'Eingabe nicht möglich. Bitte erneut versuchen.']);
                }
            }

            if (false == $auth && $request->post('wlanpin') == $this->getPin() && !empty($mac)) {
                $auth = true;
                $this->clearPin();
            }

            if ($auth) {
                $this->app->log("{$request->server('REMOTE_ADDR')}", $mac, 'AUTH');
                file_put_contents($this->app->config('wlan_guest_dir') . $mac, $ip . "\n");
                $response = new Response();
                return $response->redirect('/');
            }

            $this->app->log("{$request->server('REMOTE_ADDR')}", $mac, 'FAIL');
            file_put_contents($this->app->config('fail_log'), $mac . "\n", FILE_APPEND);
            sleep(2);
            return $this->renderView('index', ['error' => 'PIN ungültig']);
        }
        $response = new Response();
        return $response->redirect('/');
    }

    public function logout(Request $request)
    {
        if ($request->post('logout') === 'yes') {
            $mac = $this->getMac($request);
            if (file_exists($this->app->config('wlan_guest_dir') . $mac)) {
                unlink($this->app->config('wlan_guest_dir') . $mac);
            }
            $this->app->log("{$request->server('REMOTE_ADDR')}", $mac, 'LOGOUT');
        }
        $response = new Response();
        return $response->redirect('/');
    }

}