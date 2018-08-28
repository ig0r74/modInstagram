<?php

return [
    'modInstagram' => [
        'file' => 'modinstagram',
        'description' => 'modInstagram snippet to list posts',
        'properties' => [
            'accessToken' => [
                'type' => 'textfield',
                'value' => '',
            ],
            'tpl' => [
                'type' => 'textfield',
                'value' => 'tpl.modInstagram.item',
            ],
            'tplWrapper' => [
                'type' => 'textfield',
                'value' => 'tpl.modInstagram.wrapper',
            ],
            'limit' => [
                'type' => 'numberfield',
                'value' => false,
            ],
            'maxId' => [
                'type' => 'textfield',
                'value' => '',
            ],
            'minId' => [
                'type' => 'textfield',
                'value' => '',
            ],
            'toPlaceholder' => [
                'type' => 'combo-boolean',
                'value' => false,
            ],
            'showLog' => [
                'type' => 'combo-boolean',
                'value' => false,
            ],
        ],
    ],
];