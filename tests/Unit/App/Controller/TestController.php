<?php

namespace App\Controller;

use App\Exception\UnauthorizedException;
use App\Http\Response;

class TestController
{
    public function foo()
    {
        return new Response(200, 'FOO!');
    }

    public function bar()
    {
        throw new UnauthorizedException();
    }

    public function error()
    {
        throw new \Exception('all broken :(');
    }
}