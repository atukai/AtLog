<?php

namespace AtLog;

use Zend\Log\Logger;
use Zend\Log\Writer\Db;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LogFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return Logger
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config')['at_log'];

        $logger = new Logger($config['register_handlers']);
        $plugins = $logger->getWriterPluginManager();

        foreach ($config['writers'] as $name => $options) {
            if (!$options['enabled']) {
                continue;
            }

            if ($name === 'db') {
                $writer = new Db(
                    $serviceLocator->get('Zend\Db\Adapter\Adapter'),
                    $config['writers']['db']['table'],
                    $config['writers']['db']['columnMap']
                );
                $writer->setFormatter(new \Zend\Log\Formatter\Db('Y-m-d H:i:s'));
            } else {
                $writer = $plugins->get($name, $options);
            }

            $logger->addWriter($writer);
        }

        return $logger;
    }
}