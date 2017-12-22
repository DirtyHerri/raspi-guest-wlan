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
        return $this->renderView('admin', ['pin' => $this->getPin()]);
    }

    public function toggle(Request $request)
    {
        $this->auth($request);
        if ($this->app->config('DEBUG')) {
            $status = $request->post('toggle');
        } else {
            $status = shell_exec('sudo /usr/local/bin/togglewlan');
        }
        return new Response(200, $status);
    }

    public function status(Request $request)
    {
        $this->auth($request);
        if ($this->app->config('DEBUG')) {
            $status = rand(0,1);
        } else {
            $status = shell_exec('/usr/local/bin/togglewlan --status');
        }
        return new Response(200, $status);
    }

    public function pin(Request $request)
    {
        $this->auth($request);
        return new Response(200, $this->getPin());
    }

    protected function auth(Request $request)
    {
        if (false == $request->isPrivateSubnet($this->app->config('SUBNET_PRIVATE'))) {
            throw new UnauthorizedException();
        }
    }

}