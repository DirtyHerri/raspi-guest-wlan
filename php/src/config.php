<?php

return [
    'views'              => realpath(__DIR__ . '/../views') . '/',
    'logs'               => realpath(__DIR__ . '/../log') . '/',
    'subnet_private'     => ['192.168.2.', '10.8.0.'],
    'wlan_guest_dir'     => '/var/www/wlanguests/',
    'wlan_pin_file'      => '/var/www/wlanguests/wlanpin',
    'wlan_pin_list_file' => '/var/www/wlanguests/wlanpinlist',
    'fail_log'           => '/var/www/wlanguests/fail',
    'arp_table'          => '/proc/net/arp',
    'ssid'               => function () {
        return shell_exec('grep --only-matching --perl-regex "(?<=ssid\=).*" /etc/hostapd/hostapd.conf');
    },
];
