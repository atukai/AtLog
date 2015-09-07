<?php

return [
    'at_log' => [
        'register_handlers' => [
            'errorhandler'                 => false,
            'exceptionhandler'             => true,
            'fatal_error_shutdownfunction' => true,
        ],

        'writers' => [
            'db' => [
                'enabled'   => true,
                'table'     => 'logs',
                'columnMap' => [
                    'timestamp'    => 'date',
                    'priority'     => 'priority',
                    'priorityName' => 'priorityName',
                    'message'      => 'message',
                    'extra'        => [
                        'uri'   => 'uri',
                        'ip'    => 'ip',
                        'file'  => 'file'
                    ],
                ],
/*                'options' => [
                    'formatter' => [
                        'name' => 'db',
                        'options' => [
                            'dateTimeFormat' => 'Y-m-d H:i:s',
                        ],
                    ],
                ],*/
            ],
            'firephp'   => [
                'enabled' => false,
                //'check_dependency' => 'FirePHP',
            ],
            'chromephp' => [
                'enabled' => false,
                //'check_dependency' => 'ChromePhp',
            ],
            'stream'    => [
                'enabled'                  => false,
                'fingers_crossed'          => true,
                'fingers_crossed_priority' => \Zend\Log\Logger::ERR,
                'priority'                 => \Zend\Log\Logger::INFO,
                'stream'                   => $_SERVER['DOCUMENT_ROOT'] . '/../data/logs/application.log',
            ],
        ]
    ],
];