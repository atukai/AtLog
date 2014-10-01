<?php

return array(
    'at_log' => array(
        'register_error_handler' => false,
        'register_exception_handler' => true,
        'register_error_shutdown_function' => true,

        'writers' => array(
            'db'        => array(
                'enabled' => true,
                'tableName' => 'logs',
                'columnMap' => array(
                    'timestamp'    => 'date',
                    'priority'     => 'priority',
                    'priorityName' => 'priorityName',
                    'message'      => 'message',
                    'extra'        => array(
                        'url' => 'url',
                        'ip'  => 'ip'
                    ),
                )
            ),
            'firephp'   => array(
                'enabled' => false,
                //'check_dependency' => 'FirePHP',
            ),
            'chromephp' => array(
                'enabled' => false,
                //'check_dependency' => 'ChromePhp',
            ),
            'stream'    => array(
                'enabled'                  => true,
                'fingers_crossed'          => true,
                'fingers_crossed_priority' => \Zend\Log\Logger::ERR,
                'priority'                 => \Zend\Log\Logger::INFO,
                'stream'                   => 'data/log/application.log',
            ),
        )
    ),
);