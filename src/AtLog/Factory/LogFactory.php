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

        $logger = new Logger();
        $logger->addWriter(new Db(
            $serviceLocator->get('Zend\Db\Adapter\Adapter'),
            $logConfig['writers']['db']['tableName'],
            $logConfig['writers']['db']['columnMap']
        ));

        return $logger;
    }
}
