<?php

namespace Common\SessionServices\Factory;

use Common\SessionServices\SessionService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class SessionServiceFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator) {
        $sessionContainer = new Container('user');
        $sessionService = new SessionService();
        $sessionService->setSessionContainer($sessionContainer);
        return $sessionService;
    }

}
