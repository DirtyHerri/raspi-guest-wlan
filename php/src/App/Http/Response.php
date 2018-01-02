<?php

namespace App\Http;

class Response
{
    protected $headers;
    protected $code;
    protected $body;

    public function __construct($code = 200, $body = '', $headers = [])
    {
        $this->headers = $headers;
        $this->code    = $code;
        $this->body    = $body;
    }

    public function sendHeaders()
    {
        if (headers_sent()) {
            throw new \RuntimeException('Headers already send!');
        }
        if (is_array($this->headers)) {
            foreach ($this->headers as $key => $value) {
                header($key . ': ' . $value);
            }
        }
    }

    public function send()
    {
        if (false == headers_sent()) {
            http_response_code($this->code);
            $this->sendHeaders();
        }
        return $this->body;
    }

    /**
     * @param $file
     * @param array $params
     * @return Response
     */
    public function render($file, $params = [])
    {
        ob_start();
        extract($params);
        include $file;
        $this->body = ob_get_clean();
        return $this;
    }

    /**
     * @param $target
     * @param int $code
     * @return Response
     */
    public function redirect($target, $code = 302)
    {
        $this->code                = $code;
        $this->body                = '';
        $this->headers['Location'] = $target;
        return $this;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

}