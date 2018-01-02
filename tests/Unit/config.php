<?php

return [
    'views'              => realpath(__DIR__ . '/../../php/views') . '/',
    'subnet_private'     => ['127.0.0.', '10.3.1.', '192.168.2.'],
    'wlan_guest_dir'     => __DIR__ . '/../storage/',
    'wlan_pin_file'      => realpath(__DIR__ . '/../storage') . '/wlanpin',
    'wlan_pin_list_file' => realpath(__DIR__ . '/../storage') . '/wlanpinlist',
    'fail_log'           => realpath(__DIR__ . '/../storage') . '/fail',
    'arp_table'          => realpath(__DIR__ . '/../Fixtures') . '/arp',
    'ssid'               => function () {
        return "test ssid";
    },
];