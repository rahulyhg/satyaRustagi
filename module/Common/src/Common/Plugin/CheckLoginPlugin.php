<?php

namespace Common\Plugin;

use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class CheckLoginPlugin extends AbstractPlugin {

    public function __invoke()
    {
       $auth = new AuthenticationService();
        if ($auth->getIdentity() && !in_array($auth->getIdentity()->role, array('user'))) {
            $controller = $this->getController();
            $redirector = $controller->getPluginManager()->get('Redirect');
            $redirector->toRoute('user',array('action'=>'login'));

            //return $response;
        }
    } 

}
