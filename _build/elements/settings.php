<?php

return [
    'acess_token' => [
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'modinstagram_main',
    ],
    'username' => [
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'modinstagram_main',
    ],
    'password' => [
        'xtype' => 'text-password',
        'value' => '',
        'area' => 'modinstagram_main',
    ],
    'proxy_address' => [
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'modinstagram_proxy',
    ],
    'proxy_port' => [
        'xtype' => 'textfield',
        'value' => '8080',
        'area' => 'modinstagram_proxy',
    ],
    'proxy_tunnel' => [
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'modinstagram_proxy',
    ],
    'proxy_timeout' => [
        'xtype' => 'numberfield',
        'value' => 30,
        'area' => 'modinstagram_proxy',
    ],
    'proxy_user' => [
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'modinstagram_proxy',
    ],
    'proxy_pass' => [
        'xtype' => 'text-password',
        'value' => '',
        'area' => 'modinstagram_proxy',
    ],
    'proxy_method' => [
        'xtype' => 'textfield',
        'value' => 'CURLAUTH_BASIC',
        'area' => 'modinstagram_proxy',
    ],
];