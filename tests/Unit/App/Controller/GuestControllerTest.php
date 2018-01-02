<?php

namespace App\Controller;

use PHPUnit\Framework\TestCase;
use Tests\Unit\CreatesApplication;

function sleep()
{
    return;
}

class GuestControllerTest extends TestCase
{

    use CreatesApplication;

    protected $adminIp = '192.168.2.10';
    protected $guestIp = '192.168.255.240';
    protected $mac     = '58585250565b';

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
        if (file_exists($this->app->config('wlan_guest_dir') . $this->mac)) {
            unlink($this->app->config('wlan_guest_dir') . $this->mac);
        }
    }

    public function testIndexAdmin()
    {
        $this->createApp([], [], ['REMOTE_ADDR' => $this->adminIp]);
        $c        = new GuestController($this->app);
        $response = $c->index($this->request);
        $this->assertEquals(302, $response->getCode());
        $this->assertEquals('/admin', $response->getHeaders()['Location']);
    }

    public function testIndexGuest()
    {
        $this->createApp([], [], ['REMOTE_ADDR' => $this->guestIp]);
        $c        = new GuestController($this->app);
        $response = $c->index($this->request);
        $this->assertEquals(200, $response->getCode());

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($response->getBody());
        $forms = $doc->getElementsByTagName('form');
        $this->assertTrue($forms->length >= 1);
        $this->assertEquals('/login', $forms[0]->getAttribute('action'));
    }

    public function testIndexGuestExistingPin()
    {
        $this->createApp([], [], ['REMOTE_ADDR' => $this->guestIp]);
        file_put_contents($this->app->config('wlan_pin_file'), 'foopin');
        $c        = new GuestController($this->app);
        $response = $c->index($this->request);
        $this->assertEquals(200, $response->getCode());

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($response->getBody());
        $forms = $doc->getElementsByTagName('form');
        $this->assertTrue($forms->length >= 1);
        $this->assertEquals('/login', $forms[0]->getAttribute('action'));
    }

    public function testIndexGuestLoggedIn()
    {
        $this->createApp([], [], ['REMOTE_ADDR' => $this->guestIp]);

        file_put_contents($this->app->config('wlan_guest_dir') . $this->mac, $this->guestIp . "\n");

        $c        = new GuestController($this->app);
        $response = $c->index($this->request);
        $this->assertEquals(200, $response->getCode());

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($response->getBody());
        $forms = $doc->getElementsByTagName('form');
        $this->assertTrue($forms->length >= 1);
        $this->assertEquals('/logout', $forms[0]->getAttribute('action'));
    }

    public function testIndexGuestRedirect()
    {
        $this->createApp([], [], ['REMOTE_ADDR' => $this->guestIp, 'SERVER_NAME' => 'foo', 'SERVER_ADDR' => 'bar']);
        $c        = new GuestController($this->app);
        $response = $c->index($this->request);
        $this->assertEquals(302, $response->getCode());
        $this->assertEquals('http://bar/', $response->getHeaders()['Location']);
    }

    public function testIndexGuestLogout()
    {
        $this->createApp([], ['logout' => 'yes'], ['REMOTE_ADDR' => $this->guestIp, 'SERVER_NAME' => 'foo',
                                                   'SERVER_ADDR' => 'bar']);
        file_put_contents($this->app->config('wlan_guest_dir') . $this->mac, $this->guestIp . "\n");
        $this->assertFileExists($this->app->config('wlan_guest_dir') . $this->mac);
        $c        = new GuestController($this->app);
        $response = $c->logout($this->request);
        $this->assertEquals(302, $response->getCode());
        $this->assertEquals('/', $response->getHeaders()['Location']);
        $this->assertFileNotExists($this->app->config('wlan_guest_dir') . $this->mac);
    }

    public function testIndexGuestLoginFailed()
    {
        $this->createApp([], ['acceptagb' => 'yes'], ['REMOTE_ADDR' => $this->guestIp]);

        $this->assertFileNotExists($this->app->config('wlan_guest_dir') . $this->mac);
        $c        = new GuestController($this->app);
        $response = $c->login($this->request);
        $this->assertEquals(200, $response->getCode());
        $this->assertTrue(strpos($response->getBody(), 'PIN ungültig') > 0);
        $this->assertFileNotExists($this->app->config('wlan_guest_dir') . $this->mac);
    }

    public function testIndexGuestLoginSkipped()
    {
        $this->createApp([], ['acceptagb' => 'no'], ['REMOTE_ADDR' => $this->guestIp]);

        $this->assertFileNotExists($this->app->config('wlan_guest_dir') . $this->mac);
        $c        = new GuestController($this->app);
        $response = $c->login($this->request);
        $this->assertEquals(302, $response->getCode());
        $this->assertEquals('/', $response->getHeaders()['Location']);
        $this->assertFileNotExists($this->app->config('wlan_guest_dir') . $this->mac);
    }

    public function testIndexGuestLoginFromPinFile()
    {
        $this->createApp([], ['acceptagb' => 'yes', 'wlanpin' => 'foopin'], ['REMOTE_ADDR' => $this->guestIp]);

        file_put_contents($this->app->config('wlan_pin_file'), 'foopin');

        $this->assertFileNotExists($this->app->config('wlan_guest_dir') . $this->mac);
        $c        = new GuestController($this->app);
        $response = $c->login($this->request);
        $this->assertEquals(302, $response->getCode());
        $this->assertEquals('/', $response->getHeaders()['Location']);
        $this->assertFileExists($this->app->config('wlan_guest_dir') . $this->mac);
        $this->assertFileNotExists($this->app->config('wlan_pin_file'));
    }

    public function testIndexGuestLoginFromPinList()
    {
        $this->createApp([], ['acceptagb' => 'yes', 'wlanpin' => 'foopin2'], ['REMOTE_ADDR' => $this->guestIp]);

        file_put_contents($this->app->config('wlan_pin_list_file'), "foopin1\nfoopin2\nfoopin3\n");

        $pinList = file($this->app->config('wlan_pin_list_file'));
        $this->assertTrue(count($pinList) === 3);

        $this->assertFileNotExists($this->app->config('wlan_guest_dir') . $this->mac);
        $c        = new GuestController($this->app);
        $response = $c->login($this->request);
        $this->assertEquals(302, $response->getCode());
        $this->assertEquals('/', $response->getHeaders()['Location']);
        $this->assertFileExists($this->app->config('wlan_guest_dir') . $this->mac);
        $this->assertFileExists($this->app->config('wlan_pin_list_file'));

        $pinList = file($this->app->config('wlan_pin_list_file'));
        $this->assertTrue(count($pinList) === 2);
    }

    public function testIndexGuestLoginLockFail()
    {
        $this->createApp([], ['acceptagb' => 'yes', 'wlanpin' => 'foopin2'], ['REMOTE_ADDR' => $this->guestIp]);
        file_put_contents($this->app->config('wlan_pin_list_file'), "foopin1\nfoopin2\nfoopin3\n");
        $fp = fopen($this->app->config('wlan_pin_list_file'), "r+");
        flock($fp, LOCK_EX);
        $c        = new GuestController($this->app);
        $response = $c->login($this->request);
        $this->assertTrue(strpos($response->getBody(), 'Eingabe nicht möglich. Bitte erneut versuchen.') > 0);
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    public function testIndexGuestLoginFail()
    {
        $this->createApp([], ['acceptagb' => 'yes', 'wlanpin' => 'foopin2'], ['REMOTE_ADDR' => $this->guestIp]);

        for ($i = 0; $i <= 5; $i++) {
            file_put_contents($this->app->config('fail_log'), $this->mac . "\n", FILE_APPEND);
        }

        $c        = new GuestController($this->app);
        $response = $c->login($this->request);
        $this->assertEquals(200, $response->getCode());
        $this->assertTrue(strpos($response->getBody(), 'Zu viele Versuche') > 0);
    }

}