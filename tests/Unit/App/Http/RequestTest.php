<?php

namespace App\Http;


use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testQuery()
    {
        $request = $this->createRequest();
        $this->assertEquals('bar', $request->query('foo'));
        $this->assertEquals('default', $request->query('foo_2', 'default'));
    }

    public function testPost()
    {
        $request = $this->createRequest();
        $this->assertEquals('post_bar', $request->post('post_foo'));
        $this->assertEquals('post_default', $request->post('post_foo_2', 'post_default'));
    }

    public function testServer()
    {
        $request = $this->createRequest();
        $this->assertEquals('192.168.255.240', $request->server('REMOTE_ADDR'));
        $this->assertEquals(null, $request->server('X-UNKNOWN'));
    }

    public function testGetPath()
    {
        $request = $this->createRequest();
        $this->assertEquals('/foo/bar', $request->getPath());
    }

    public function testGetMethod()
    {
        $request = $this->createRequest();
        $this->assertEquals('POST', $request->getMethod());
    }

    public function testIsPrivateSubnet()
    {
        $request = $this->createRequest();
        $this->assertTrue($request->isPrivateSubnet('192.168.255.'));
        $this->assertFalse($request->isPrivateSubnet('192.168.0.'));
    }

    public function testGetMac()
    {
        $request = $this->createRequest();
        $mac     = $request->getMac(__DIR__ . '/../../../Fixtures/arp');
        $this->assertEquals('58585250565b', $mac);
    }

    protected function createRequest()
    {
        $get  = ['foo' => 'bar'];
        $post = ['post_foo' => 'post_bar'];

        $server                   = [];
        $server['REMOTE_ADDR']    = '192.168.255.240';
        $server['REQUEST_URI']    = '/foo/bar?x=1';
        $server['REQUEST_METHOD'] = 'POST';
        return new \App\Http\Request($get, $post, $server);
    }
}
