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
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, function ($event) use ($serviceManager, $logger) {
            /* @var \Exception */
            $exception = $event->getResult()->exception;

            if (!$exception) {
                return;
            }

            do {
                $logger->err($exception->getMessage(), array(
                    'uri'   => $serviceManager->get('Request')->getRequestUri(),
                    'ip'    => sprintf('%u', ip2long($serviceManager->get('Request')->getServer('REMOTE_ADDR'))),
                    'file'  => $exception->getFile() . ' at line ' . $exception->getLine(),
                    'trace' => $exception->getTraceAsString(),
                ));
            }
            while($exception = $exception->getPrevious());
        });

        /*// Log events
        $sem = $eventManager->getSharedManager();
        $sem->attach('*', '*',
            function ($e)
            {
                $event = $e->getName();
                $target = get_class($e->getTarget());
                $params = $e->getParams();
                $output = sprintf(
                    'Event "%s" was triggered on target "%s", with parameters %s\n',
                    $event,
                    $target,
                    json_encode($params)
                );

                file_put_contents(APPLICATION_PATH . '/data/logs/events.txt', $output, FILE_APPEND);

                // Return true so this listener doesn't break the validator
                // chain triggering session.validate listeners
                return true;
            }
        );*/
    }
}