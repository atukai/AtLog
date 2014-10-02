<?php

return array(
    'at_log' => array(
        'register_handlers' => array(
            'errorhandler'                 => false,
            'exceptionhandler'             => true,
            'fatal_error_shutdownfunction' => true,
        ),

        'writers' => array(
            'db'        => array(
                'enabled' => true,
                'table'   => 'logs',
                'columnMap'  => array(
                    'timestamp'    => 'date',
                    'priority'     => 'priority',
                    'priorityName' => 'priorityName',
                    'message'      => 'message',
                    'extra'        => array(
                        'uri' => 'uri',
                        'ip'  => 'ip'
                    ),
                )
            ),
            'firephp'   => array(
                'enabled' => false,
                //'check_dependency' => 'FirePHP',
            ),
            'chromephp' => array(
                'enabled' => true,
                //'check_dependency' => 'ChromePhp',
            ),
            'stream'    => array(
                'enabled'                  => true,
                'fingers_crossed'          => true,
                'fingers_crossed_priority' => \Zend\Log\Logger::ERR,
                'priority'                 => \Zend\Log\Logger::INFO,
                'stream'                   => APPLICATION_PATH . '/data/logs/application.log',
            ),
        )
    ),
);