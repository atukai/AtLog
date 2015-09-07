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
        $serviceManager = $app->getServiceManager();
        $logger = $serviceManager->get('at_logger');

        $app->getEventManager()->attach(MvcEvent::EVENT_DISPATCH_ERROR, function ($event) use ($serviceManager, $logger) {
            /* @var \Exception */
            $exception = $event->getResult()->exception;

            if (!$exception) {
                return;
            }

            do {
                $logger->err($exception->getMessage(), [
                    'uri'   => $serviceManager->get('Request')->getRequestUri(),
                    'ip'    => sprintf('%u', ip2long($serviceManager->get('Request')->getServer('REMOTE_ADDR'))),
                    'file'  => $exception->getFile() . ' at line ' . $exception->getLine(),
                ]);
            } while ($exception = $exception->getPrevious());
        });
    }
}