<?php

namespace AtLog;

use Zend\Log\Logger;
use Zend\Mvc\MvcEvent;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return include __DIR__ . '/config/service.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getApplication();
        $serviceManager = $app->getServiceManager();
        $eventManager = $app->getEventManager();

        $logger = $serviceManager->get('at_logger');
        $eventManager->attach('dispatch.error', function ($event) use ($serviceManager, $logger) {
            $exception = $event->getResult()->exception;
            if (!$exception) {
                return;
            }

            $logger->crit($exception, array(
                'uri' => $serviceManager->get('Request')->getRequestUri(),
                'ip' => ip2long($serviceManager->get('Request')->getServer('REMOTE_ADDR')),
            ));
        });

        // Log events
        $eventManager->attach('*',
            function ($e)
            {
                $event = $e->getName();
                $target = get_class($e->getTarget());
                $params = $e->getParams();
                $output = sprintf(
                    'Event "%s" was triggered on target "%s", with parameters %s\r\n',
                    $event,
                    $target,
                    json_encode($params));

                file_put_contents(APPLICATION_PATH . '/data/logs/events.txt', $output, FILE_APPEND);

                // Return true so this listener doesn't break the validator
                // chain triggering session.validate listeners
                return true;
            }
        );
    }
}