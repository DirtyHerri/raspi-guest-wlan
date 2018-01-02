<?php

namespace App\Http;


use PHPUnit\Framework\TestCase;

$headersSent         = [];
$headers_sent_return = false;

function header($h)
{
    global $headersSent;
    $headersSent[] = $h;
}

function headers_sent()
{
    global $headers_sent_return;
    return $headers_sent_return;
}

class ResponseTest extends TestCase
{

    public function testSendHeaders()
    {
        global $headersSent;
        $headersSent = [];
        $response    = new Response(200, '', ['X-FOO' => 1, 'X-BAR' => 2]);
        $response->sendHeaders();
        $this->assertEquals(['X-FOO: 1', 'X-BAR: 2'], $headersSent);
    }

    public function testSendHeadersAlreadySent()
    {
        global $headers_sent_return;
        $headers_sent_return = true;
        $this->expectException(\RuntimeException::class);
        $response = new Response(200, '', ['X-FOO' => 1, 'X-BAR' => 2]);
        $response->sendHeaders();
    }

    public function testSend()
    {
        global $headersSent;
        global $headers_sent_return;
        $headersSent         = [];
        $headers_sent_return = false;
        $response            = new Response(200, 'Test Body', ['X-FOO' => 4, 'X-BAR' => 5]);
        $body                = $response->send();
        $this->assertEquals('Test Body', $body);
        $this->assertEquals(200, $response->getCode());
    }

    public function testRender()
    {
        $response = new Response();
        $response->render(__DIR__ . '/../../../Fixtures/testView.php', ['p1' => 1, 'p2' => 2]);
        $this->assertEquals('Test View 1 2', $response->getBody());
        $response->render(__DIR__ . '/../../../Fixtures/testView.php');
        $this->assertEquals('Test View', $response->getBody());
    }

    public function testRedirect()
    {
        global $headersSent;
        $headersSent = [];
        $response    = new Response();
        $response->redirect('/target');
        $body = $response->send();
        $this->assertEquals(['Location' => '/target'], $response->getHeaders());
        $this->assertEquals(['Location: /target'], $headersSent);
        $this->assertEmpty($body);
    }

}

