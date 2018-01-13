<?php

namespace App\Controller;

use App\Exception\UnauthorizedException;
use PHPUnit\Framework\TestCase;
use Tests\Unit\CreatesApplication;

$mockShellExec   = false;
$shellExecReturn = 1;

function shell_exec($cmd)
{
    global $mockShellExec;
    global $shellExecReturn;
    if ($mockShellExec) {
        return $shellExecReturn;
    } else {
        return \shell_exec($cmd);
    }
}

class AdminControllerTest extends TestCase
{

    use CreatesApplication;

    protected $adminIp = '192.168.2.10';

    /**
     * @after
     */
    public function tearDown()
    {
        global $mockShellExec;
        global $shellExecReturn;
        $mockShellExec   = false;
        $shellExecReturn = 1;
        if (file_exists($this->app->config('wlan_pin_file'))) {
            unlink($this->app->config('wlan_pin_file'));
        }
        if (file_exists($this->app->config('wlan_pin_list_file'))) {
            unlink($this->app->config('wlan_pin_list_file'));
        }
        if (file_exists($this->app->config('fail_log'))) {
            unlink($this->app->config('fail_log'));
        }
    }

    public function testIndexUnauthorized()
    {
        $this->expectException(UnauthorizedException::class);
        $this->createApp([], [], ['REMOTE_ADDR' => '8.8.8.8']);
        $c = new AdminController($this->app);
        $c->index($this->request);
    }

    public function testIndexWithoutPin()
    {
        $this->createApp([], [], ['REMOTE_ADDR' => $this->adminIp]);
        $c        = new AdminController($this->app);
        $response = $c->index($this->request);
        $this->assertEquals(200, $response->getCode());

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($response->getBody());

        $this->assertEquals('......', $doc->getElementById('pin')->textContent);
    }

    public function testIndexWithPin()
    {
        $this->createApp([], [], ['REMOTE_ADDR' => $this->adminIp]);
        file_put_contents($this->app->config('wlan_pin_file'), 'foopin');

        $c        = new AdminController($this->app);
        $response = $c->index($this->request);
        $this->assertEquals(200, $response->getCode());

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($response->getBody());

        $this->assertEquals('foopin', $doc->getElementById('pin')->textContent);
    }

    public function testIndexWithFailLog()
    {
        $this->createApp([], [], ['REMOTE_ADDR' => $this->adminIp]);
        file_put_contents($this->app->config('fail_log'), 'foomac');

        $c        = new AdminController($this->app);
        $response = $c->index($this->request);
        $this->assertEquals(200, $response->getCode());

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($response->getBody());

        $forms = $doc->getElementsByTagName('form');
        $this->assertTrue($forms->length >= 1);
        $this->assertEquals('/clear_failed', $forms[0]->getAttribute('action'));
    }

    public function testPinListNoList()
    {
        $this->createApp([], [], ['REMOTE_ADDR' => $this->adminIp, 'REQUEST_METHOD' => 'GET']);
        $c        = new AdminController($this->app);
        $response = $c->pinList($this->request);
        $this->assertEquals(200, $response->getCode());
        $this->assertFileNotExists($this->app->config('wlan_pin_list_file'));
    }

    public function testPinList()
    {
        $this->createApp([], [], ['REMOTE_ADDR' => $this->adminIp, 'REQUEST_METHOD' => 'GET']);
        file_put_contents($this->app->config('wlan_pin_list_file'), "foopin1\nfoopin2\nfoopin3\n");
        $c        = new AdminController($this->app);
        $response = $c->pinList($this->request);
        $this->assertEquals(200, $response->getCode());

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($response->getBody());
        $pins = $doc->getElementById('pin-list');
        $this->assertEquals(3, $pins->getElementsByTagName('li')->length);
    }

    public function testCreatePinList()
    {
        $this->createApp([], [], ['REMOTE_ADDR' => $this->adminIp, 'REQUEST_METHOD' => 'POST']);
        $this->assertFileNotExists($this->app->config('wlan_pin_list_file'));
        $c        = new AdminController($this->app);
        $response = $c->pinList($this->request);
        $this->assertEquals(302, $response->getCode());
        $this->assertFileExists($this->app->config('wlan_pin_list_file'));
    }

    public function testToggleOn()
    {
        global $mockShellExec;
        global $shellExecReturn;
        $mockShellExec   = true;
        $shellExecReturn = 1;
        $this->createApp([], [], ['REMOTE_ADDR' => $this->adminIp, 'REQUEST_METHOD' => 'POST']);
        $c        = new AdminController($this->app);
        $response = $c->toggle($this->request);
        $this->assertEquals(200, $response->getCode());
        $this->assertEquals("1", $response->getBody());
    }

    public function testToggleOff()
    {
        global $mockShellExec;
        global $shellExecReturn;
        $mockShellExec   = true;
        $shellExecReturn = 0;
        $this->createApp([], [], ['REMOTE_ADDR' => $this->adminIp, 'REQUEST_METHOD' => 'POST']);
        $c        = new AdminController($this->app);
        $response = $c->toggle($this->request);
        $this->assertEquals(200, $response->getCode());
        $this->assertEquals("0", $response->getBody());
    }

    public function testStatusOn()
    {
        global $mockShellExec;
        global $shellExecReturn;
        $mockShellExec   = true;
        $shellExecReturn = 1;
        $this->createApp([], [], ['REMOTE_ADDR' => $this->adminIp, 'REQUEST_METHOD' => 'GET']);
        $c        = new AdminController($this->app);
        $response = $c->status($this->request);
        $this->assertEquals(200, $response->getCode());
        $this->assertEquals("1", $response->getBody());
    }

    public function testStatusOff()
    {
        global $mockShellExec;
        global $shellExecReturn;
        $mockShellExec   = true;
        $shellExecReturn = 0;
        $this->createApp([], [], ['REMOTE_ADDR' => $this->adminIp, 'REQUEST_METHOD' => 'GET']);
        $c        = new AdminController($this->app);
        $response = $c->status($this->request);
        $this->assertEquals(200, $response->getCode());
        $this->assertEquals("0", $response->getBody());
    }

    public function testPin()
    {
        $this->createApp([], [], ['REMOTE_ADDR' => $this->adminIp, 'REQUEST_METHOD' => 'GET']);
        file_put_contents($this->app->config('wlan_pin_file'), 'foopin');
        $c        = new AdminController($this->app);
        $response = $c->pin($this->request);
        $this->assertEquals(200, $response->getCode());
        $this->assertEquals("foopin", $response->getBody());
    }

    public function testPinNoPin()
    {
        $this->createApp([], [], ['REMOTE_ADDR' => $this->adminIp, 'REQUEST_METHOD' => 'GET']);
        $c        = new AdminController($this->app);
        $response = $c->pin($this->request);
        $this->assertEquals(200, $response->getCode());
        $this->assertEquals("......", $response->getBody());
    }

    public function testClearFailed()
    {
        $this->createApp([], [], ['REMOTE_ADDR' => $this->adminIp, 'REQUEST_METHOD' => 'GET']);
        file_put_contents($this->app->config('fail_log'), 'foomac');
        $this->assertFileExists($this->app->config('fail_log'));
        $c        = new AdminController($this->app);
        $response = $c->clearFailed($this->request);
        $this->assertEquals(302, $response->getCode());
        $this->assertFileNotExists($this->app->config('fail_log'));
    }

    public function testReboot()
    {
        global $mockShellExec;
        global $shellExecReturn;
        $mockShellExec   = true;
        $shellExecReturn = 1;
        $this->createApp([], [], ['REMOTE_ADDR' => $this->adminIp, 'REQUEST_METHOD' => 'POST']);
        $c        = new AdminController($this->app);
        $response = $c->reboot($this->request);
        $this->assertEquals(200, $response->getCode());
        $this->assertEquals("1", $response->getBody());
    }

    public function testHalt()
    {
        global $mockShellExec;
        global $shellExecReturn;
        $mockShellExec   = true;
        $shellExecReturn = 1;
        $this->createApp([], [], ['REMOTE_ADDR' => $this->adminIp, 'REQUEST_METHOD' => 'POST']);
        $c        = new AdminController($this->app);
        $response = $c->halt($this->request);
        $this->assertEquals(200, $response->getCode());
        $this->assertEquals("1", $response->getBody());
    }
}