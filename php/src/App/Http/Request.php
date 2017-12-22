<?php

namespace App\Http;

class Request
{
    protected $_post;
    protected $_query;
    protected $_server;

    public function __construct(array $query, array $post, array $server)
    {
        $this->_query   = $query;
        $this->_post    = $post;
        $this->_server  = $server;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function query($key, $default = null)
    {
        return isset($this->_query[$key]) ? $this->_query[$key] : $default;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function post($key, $default = null)
    {
        return isset($this->_post[$key]) ? $this->_post[$key] : $default;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function server($key, $default = null)
    {
        return isset($this->_server[$key]) ? $this->_server[$key] : $default;
    }

    public function getMac($arpTablePath)
    {
        $ip  = $this->server('REMOTE_ADDR');
        $arp = file($arpTablePath);
        $mac = null;
        if ($arp !== false) {
            foreach ($arp as $entry) {
                if (0 === strpos($entry, $ip . " ")) {
                    $mac = str_replace(':', '', substr($entry, 41, 17));
                    break;
                }
            }
        }
        return $mac;
    }

    public function getPath()
    {
        $uri = parse_url($this->server('REQUEST_URI'));
        return $uri['path'];
    }

    public function getMethod()
    {
        return $this->server('REQUEST_METHOD');
    }

    public function isPrivateSubnet($subnet) {
        if (false == is_array($subnet)) {
            $subnet = [$subnet];
        }
        foreach ($subnet as $item) {
            if (strpos($this->server("REMOTE_ADDR"), $item) === 0) {
                return true;
            }
        }
    }
}