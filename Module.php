<?php

namespace AtLog;

use Zend\Mvc\MvcEvent;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    public function getServiceConfig()
    {
        return include __DIR__ . '/config/service.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getApplication();
        $logger = $app->getServiceManager()->get('at_logger');

        $app->getEventManager()->attach(MvcEvent::EVENT_DISPATCH_ERROR, function ($event) use ($logger) {
            /* @var \Exception */
            $exception = $event->getResult()->exception;

            if (!$exception) {
                return;
            }

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $clientIp = $_SERVER['REMOTE_ADDR'];
            }

            do {
                $logger->err($exception->getMessage(), [
                    'uri'   => $_SERVER['REQUEST_URI'],
                    'ip'    => sprintf('%u', ip2long($clientIp)),
                    'file'  => $exception->getFile() . ' at line ' . $exception->getLine(),
                ]);
            } while ($exception = $exception->getPrevious());
        });
    }
}