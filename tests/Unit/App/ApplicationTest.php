<?php

namespace App;

use App\Contracts\LoggerInterface;
use App\Http\Response;
use App\Log\Logger;
use PHPUnit\Framework\TestCase;
use Tests\Unit\CreatesApplication;

class ApplicationTest extends TestCase
{
    use CreatesApplication;

    public function testLogger()
    {
        $logger = $this->createMock(Logger::class);
        $logger
            ->method('write')
            ->with(
               $this->equalTo('11:22:33:44:55:66 foo'),
               $this->equalTo('TEST')
            );

        $this->createApp();
        $this->app->setLogger($logger);
        $this->assertInstanceOf(LoggerInterface::class, $this->app->getLogger());
        $this->app->log('foo', '112233445566', 'TEST');
    }

    public function test404Route()
    {
        $this->createApp([], [], ['REQUEST_URI' => '/foo', 'REQUEST_METHOD' => 'GET']);
        $response = $this->app->run();
        $this->assertEquals(302, $response->getCode());
    }

    public function testClosureRoute()
    {
        $this->createApp([], [], ['REQUEST_URI' => '/foo', 'REQUEST_METHOD' => 'GET']);
        $this->app->get('/foo', function(){
           return new Response(200, 'FOO!');
        });
        $response = $this->app->run();
        $this->assertEquals('FOO!', $response->getBody());
    }

    public function testControllerRoute()
    {
        $this->createApp([], [], ['REQUEST_URI' => '/foo', 'REQUEST_METHOD' => 'POST']);
        $this->app->post('/foo', 'TestController:foo');
        $response = $this->app->run();
        $this->assertEquals('FOO!', $response->getBody());
    }

    public function testControllerRouteUnauthorized()
    {
        $this->createApp([], [], ['REQUEST_URI' => '/bar', 'REQUEST_METHOD' => 'POST']);
        $this->app->post('/bar', 'TestController:bar');
        $response = $this->app->run();
        $this->assertEquals(403, $response->getCode());
        $this->assertEquals('Unauthorized', $response->getBody());
    }

    public function testControllerRouteError()
    {
        $this->createApp([], [], ['REQUEST_URI' => '/error', 'REQUEST_METHOD' => 'POST']);
        $this->app->post('/error', 'TestController:error');
        $response = $this->app->run();
        $this->assertEquals(500, $response->getCode());
        $this->assertEquals('Unknown error', $response->getBody());
    }
}