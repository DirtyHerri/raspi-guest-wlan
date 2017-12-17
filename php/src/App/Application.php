<?php

namespace App;

use App\Contracts\LoggerInterface;

class Application
{
    protected $_config = [];
    protected $_get    = [];
    protected $_post   = [];
    protected $_server = [];
    protected $logger;

    /**
     * Application constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->_config = $config;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param array $get
     */
    public function setQuery(array $get)
    {
        $this->_get = $get;
    }

    /**
     * @param array $post
     */
    public function setPost(array $post)
    {
        $this->_post = $post;
    }

    /**
     * @param array $server
     */
    public function setServer(array $server)
    {
        $this->_server = $server;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function config($key, $default = null)
    {
        return isset($this->_config[$key]) ? $this->_config[$key] : $default;
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

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function query($key, $default = null)
    {
        return isset($this->_get[$key]) ? $this->_get[$key] : $default;
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
     * Main entry point
     */
    public function run()
    {
        // Show PIN to clients inside private network
        if (strpos($this->server("REMOTE_ADDR"), $this->config('SUBNET_PRIVATE')) === 0
            && false === $this->query('debug', false))
        {
            $this->renderAdmin();
            return;
        }

        // Redirect to show real address
        if (false == $this->config('DEBUG')
            && $this->server("SERVER_NAME") != $this->server("SERVER_ADDR"))
        {
            header("Location: http://" . $this->server("SERVER_ADDR") . "/");
            return;
        }

        $pin = $this->getPin();
        $mac = $this->getMac();
        if (!empty($mac) && file_exists($this->config('WLANGUEST_DIR') . $mac)) {
            if ($this->post('logout') === 'yes') {
                if ($this->logout($mac)) {
                    $this->log("{$this->server('REMOTE_ADDR')}", $mac, 'LOGOUT');
                    $this->redirect();
                    return;
                }
            }
            $this->renderLoggedIn();
            return;
        } else if ($this->authenticate($mac, $pin)) {
            $this->rememberMac($mac);
            $this->clearPin();
            $this->redirect();
            return;
        } else {
            $this->log("{$this->server('REMOTE_ADDR')}", $mac, 'ACCESS');
            $this->createPin();
            $this->renderIndex();
            return;
        }
    }

    protected function redirect() {
        header('Location: /');
    }

    protected function renderLoggedIn() {
        require $this->config('VIEWS') . 'logged_in.php';
    }

    protected function renderIndex() {
        require $this->config('VIEWS') . 'index.php';
    }

    protected function renderAdmin() {
        $pin = $this->getPin();
        require $this->config('VIEWS') . 'admin.php';
    }

    /**
     * @return bool true, if new PIN was created
     * @throws \Exception
     */
    protected function createPin()
    {
        if (!file_exists($this->config('WLAN_PIN_FILE'))) {
            file_put_contents($this->config('WLAN_PIN_FILE'), random_int(100000, 999999) . "\n");
            return true;
        }
        return false;
    }

    protected function getPin()
    {
        $pin = file_exists($this->config('WLAN_PIN_FILE')) ? file_get_contents($this->config('WLAN_PIN_FILE')) : '';
        return trim($pin);
    }

    protected function clearPin()
    {
        if (file_exists($this->config('WLAN_PIN_FILE'))) {
            return unlink($this->config('WLAN_PIN_FILE'));
        }
    }

    /**
     * @param $mac
     * @param $pin
     * @return bool
     */
    protected function authenticate($mac, $pin)
    {
        if ($this->post('acceptagb') === "yes"
            && $this->post('wlanpin') === $pin
            && !empty($mac)) {
            return true;
        } else if ($this->post('acceptagb') === "yes"
                   && $this->post('wlanpin') !== $pin) {
            $this->log("{$this->server('REMOTE_ADDR')}", $mac, 'FAILED');
        }
    }

    /**
     * @param $mac
     * @return bool
     */
    protected function logout($mac)
    {
        if (file_exists($this->config('WLANGUEST_DIR') . $mac)) {
            return unlink($this->config('WLANGUEST_DIR') . $mac);
        }
    }

    protected function getMac()
    {
        $ip  = $this->server('REMOTE_ADDR');
        $arp = $this->getArpTable();
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

    protected function rememberMac($mac)
    {
        $this->log("{$this->server('REMOTE_ADDR')}", $mac, 'AUTH');
        file_put_contents($this->config('WLANGUEST_DIR') . $mac, $this->server('REMOTE_ADDR') . "\n");
    }

    protected function formatMac($mac) {
        $macString = '';
        for ($i = 1; $i <= strlen($mac); $i++) {
            if ($i < strlen($mac) && $i%2==0) $macString .= $mac[$i-1] . ':';
            else $macString .= $mac[$i-1];
        }
        return $macString;
    }

    protected function getArpTable()
    {
        return file($this->config('ARP_TABLE'));
    }

    protected function log($text, $mac, $type)
    {
        $macString = $this->formatMac($mac);
        $logger    = $this->getLogger();
        if ($logger) $logger->write($macString . ' ' . $text, $type);
    }

}