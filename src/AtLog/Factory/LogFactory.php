<?php

namespace AtLog\Factory;

use Zend\Log\Logger;
use Zend\Log\Writer\Db;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LogFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $appConfig = $serviceLocator->get('Config');
        $logConfig = $appConfig['at_log'];

        $logger = new Logger($logConfig['register_handlers']);
        $plugins = $logger->getWriterPluginManager();

        foreach ($logConfig['writers'] as $name => $options) {
            if (!$options['enabled']) {
                continue;
            }
            unset($options['enabled']);

            if ($name == 'db') {
                $writer = new Db(
                    $serviceLocator->get('Zend\Db\Adapter\Adapter'),
                    $logConfig['writers']['db']['table'],
                    $logConfig['writers']['db']['columnMap']
                );
            } else {
                $writer = $plugins->get($name, $options);
            }

            $logger->addWriter($writer);
        }

        return $logger;
    }
}