<?php

namespace App\Controller;

use App\Exception\UnauthorizedException;
use App\Http\Request;
use App\Http\Response;

class AdminController extends Controller
{

    public function index(Request $request)
    {
        $this->auth($request);
        $clearFailed = false;
        if (file_exists($this->app->config('fail_log'))) {
            $clearFailed = true;
        }
        return $this->renderView('admin', ['bodyClass' => 'admin', 'pin' => $this->getPin(), 'clearFailed' => $clearFailed]);
    }

    public function pinList(Request $request)
    {
        $this->auth($request);

        if ($request->getMethod() == 'POST') {
            $this->createPinList();
            $response = new Response();
            return $response->redirect('/list');
        }

        $list = $this->getPinList();
        return $this->renderView('pin_list', ['list' => $list]);
    }

    public function toggle(Request $request)
    {
        $this->auth($request);
        //@codeCoverageIgnoreStart
        if ($this->app->config('DEBUG'))
        {
            $status = $request->post('toggle');
        }//@codeCoverageIgnoreEnd
        else
        {
            $status = shell_exec('sudo /usr/local/bin/togglewlan');
        }
        return new Response(200, $status);
    }

    public function status(Request $request)
    {
        $this->auth($request);
        //@codeCoverageIgnoreStart
        if ($this->app->config('DEBUG'))
        {
            $status = rand(0,1);
        }//@codeCoverageIgnoreEnd
        else
        {
            $status = shell_exec('/usr/local/bin/togglewlan --status');
        }
        return new Response(200, $status);
    }

    public function pin(Request $request)
    {
        $this->auth($request);
        return new Response(200, $this->getPin());
    }

    public function clearFailed(Request $request)
    {
        $this->auth($request);
        if (file_exists($this->app->config('fail_log'))) {
            unlink($this->app->config('fail_log'));
        }
        $response = new Response();
        return $response->redirect('/admin');
    }

    public function reboot(Request $request)
    {
        $this->auth($request);
        if (false == $this->app->config('DEBUG'))
        {
            shell_exec('sudo /usr/local/bin/powerctl --reboot');
        }
        return new Response(200, 1);
    }

    public function halt(Request $request)
    {
        $this->auth($request);
        if (false == $this->app->config('DEBUG'))
        {
            shell_exec('sudo /usr/local/bin/powerctl --halt');
        }
        return new Response(200, 1);
    }

    protected function auth(Request $request)
    {
        if (false == $request->isPrivateSubnet($this->app->config('subnet_private'))) {
            throw new UnauthorizedException();
        }
    }

}