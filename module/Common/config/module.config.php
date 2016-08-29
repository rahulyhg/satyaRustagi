<?php

return array(
    'controllers' => array(
//        'invokables' => array(
//            'Common\Controller\Common' => 'Common\Controller\CommonController'
//        )
        'factories' => array(
            'Common\Controller\Common' => 'Common\Controller\Factory\CommonControllerFactory'
        )
    ),
    'router' => array(
        'routes' => array(
            'common' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/common[/:action[/:id]]',
                    'defaults' => array(
                        'controller' => 'Common\Controller\Common',
                        'action' => 'index'
                    )
                )
            ),
           
           
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'common' => __DIR__ . '/../view'
        ),
    ),
//    'service_manager' => array(
//        'factories' => array(
//            'Common\Mapper\PostMapperInterface' => 'Common\Mapper\Factory\ZendDbSqlMapperFactory',
//            'Common\Service\PostServiceInterface' => 'Common\Mapper\Factory\PostServiceFactory',
//            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory'
//        )
//    )
);
