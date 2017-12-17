<?php

return [
    'VIEWS'          => realpath(__DIR__ . '/../views') . '/',
    'LOGS'           => realpath(__DIR__ . '/../log') . '/',
    'SUBNET_PRIVATE' => '192.168.2.',
    'WLANGUEST_DIR'  => '/var/www/wlanguests/',
    'WLAN_PIN_FILE'  => '/var/www/wlanguests/wlanpin',
    'ARP_TABLE'      => '/proc/net/arp',
];
