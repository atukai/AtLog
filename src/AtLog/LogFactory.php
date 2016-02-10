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
                try {
                    $dbAdapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');
                    $writer = new Db(
                        $dbAdapter,
                        $config['writers']['db']['table'],
                        $config['writers']['db']['columnMap']
                    );
                    $writer->setFormatter(new \Zend\Log\Formatter\Db('Y-m-d H:i:s'));
                } catch (\Exception $e) {

                }
            } else {
                $writer = $plugins->get($name, $options);
            }

            $logger->addWriter($writer);
        }

        return $logger;
    }
}