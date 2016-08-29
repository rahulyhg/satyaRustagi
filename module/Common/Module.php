<?php
namespace Common;

use Common\Helper\MyHelper;

class Module {

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php'
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getControllerPluginConfig() {
        return array(
            'factories' => array(
                //'getUserSession' => 'Common\Plugin\SessionPluginFactory',//not in use
                'Common\Plugin\UserSessionPlugin' => 'Common\Plugin\Factory\UserSessionPluginFactory', //all session related data
                'Common\Plugin\TablePlugin' => 'Common\Plugin\Factory\TablePluginFactory',// all table related data
                'Common\Plugin\AuthFrontPlugin' => 'Common\Plugin\Factory\AuthFrontPluginFactory'
            ),
             'aliases' => array(
                'authUser' => 'Common\Plugin\AuthFrontPlugin',
                'getUser' => 'Common\Plugin\UserSessionPlugin',
                'getTable' => 'Common\Plugin\TablePlugin'
            ),
        );
    }

    public function getViewHelperConfig() {
        return array(
            'factories' => array(
                'Common\Helper\UserSessionHelper' => 'Common\Helper\Factory\UserSessionHelperFactory',
                'Common\Helper\AuthFrontHelper' => 'Common\Helper\Factory\AuthFrontHelperFactory',
                'myHelper' => function($sm) {
                    // either create a new instance of your model
                    //$model = new \Application\Model\CountryTable();
                    // or, if your model is in the servicemanager, fetch it from there
                    $dbAdapter = $sm->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
                    $model = $sm->getServiceLocator()->get('Application\Model\PostTable');
                    // $model = $sm->getServiceLocator()->get('Application\Model\PostTable');
                    // print_r($model);
                    // die;
                    // create a new instance of your helper, injecting the model it uses
                    $helper = new MyHelper($model, $dbAdapter);
                    return $helper;
                },
            ),
            'aliases' => array(
                'authUser' => 'Common\Helper\AuthFrontHelper',
                'getUser' => 'Common\Helper\UserSessionHelper'
            ),
        );
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(// main session define
                'Common\SessionServices' => 'Common\SessionServices\Factory\SessionServiceFactory',
                'Common\Cache\Redis' => 'Common\Factory\RedisFactory',
                'Common\Mapper\PostMapperInterface' => 'Common\Mapper\Factory\ZendDbSqlMapperFactory',
                'Common\Mapper\CommonMapperInterface' => 'Common\Mapper\Factory\CommonDbSqlMapperFactory',
                'Common\Service\PostServiceInterface' => 'Common\Service\Factory\PostServiceFactory',
                'Common\Service\CommonServiceInterface' => 'Common\Service\Factory\CommonServiceFactory',
                'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory'
            ),
            'aliases' => array(
                'sessionService' => 'Common\SessionServices'
            ),
        );
    }

}
