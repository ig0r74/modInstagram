<?php

return [
    'modInstagram' => [
        'file' => 'modinstagram',
        'description' => 'modInstagram snippet to list posts',
        'properties' => [
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
            'accessToken' => [
                'type' => 'textfield',
                'value' => false,
            ],
            'toPlaceholder' => [
                'type' => 'combo-boolean',
                'value' => false,
            ],
        ],
    ],
];