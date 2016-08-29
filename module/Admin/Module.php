<?php

namespace Admin;

use Admin\Model\AllCountryTable;
use Admin\Model\CityTable;
use Admin\Model\CountryTable;
use Admin\Model\DesignationTable;
use Admin\Model\EducationfieldTable;
use Admin\Model\EducationlevelTable;
use Admin\Model\Entity\AllCountries;
use Admin\Model\Entity\AllPages;
use Admin\Model\Entity\Cities;
use Admin\Model\Entity\Countries;
use Admin\Model\Entity\Designations;
use Admin\Model\Entity\Educationfields;
use Admin\Model\Entity\Educationlevels;
use Admin\Model\Entity\Events;
use Admin\Model\Entity\Gothras;
use Admin\Model\Entity\Newscategories;
use Admin\Model\Entity\Newses;
use Admin\Model\Entity\Professions;
use Admin\Model\Entity\Religions;
use Admin\Model\Entity\Starsigns;
use Admin\Model\Entity\States;
use Admin\Model\Entity\Userinfos;
use Admin\Model\Entity\UserRoles;
use Admin\Model\Entity\Users;
use Admin\Model\Entity\Usertypes;
use Admin\Model\Entity\Zodiacsigns;
use Admin\Model\EventsTable;
use Admin\Model\GothraTable;
use Admin\Model\NewscategoryTable;
use Admin\Model\NewsTable;
use Admin\Model\PagesTable;
use Admin\Model\ProfessionTable;
use Admin\Model\ReligionTable;
use Admin\Model\StarsignTable;
use Admin\Model\StateTable;
use Admin\Model\UserinfoTable;
use Admin\Model\UsersRolesTable;
use Admin\Model\UserTable;
use Admin\Model\UsertypeTable;
use Admin\Model\ZodiacsignTable;
use Admin\View\Helper\AuthHelper;
use Zend\Authentication\AuthenticationService;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

class Module implements ViewHelperProviderInterface {

    protected $whitelist = array(
        'Admin\Controller\Index',
        'Application\Controller\Index',
        'Application\Controller\Pages',
        'Application\Controller\User',
        'Application\Controller\App',
        'Application\Controller\Account',
        'Application\Controller\Membership',
        'Application\Controller\Matrimonial',
        'Application\Controller\Community',
        'Application\Controller\News',
        'Application\Controller\Events',
        'Application\Controller\Posts',
        'Application\Controller\Obituary',
        'Application\Controller\Justborn',
        'Common\Controller\Common',

        
    );

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

    public function onBootstrap(EventInterface $e) {
        $app = $e->getParam('application');
        $em = $app->getEventManager();

        $em->attach(MvcEvent::EVENT_DISPATCH, array($this, 'selectLayoutBasedOnRoute'));
        $em->attach(MvcEvent::EVENT_DISPATCH, array($this, 'checkLogin'));
    }

    /**
     * Select the admin layout based on route name
     *
     * @param  MvcEvent $e
     * @return void
     */
    public function selectLayoutBasedOnRoute(MvcEvent $e) {
        $app = $e->getParam('application');
        $sm = $app->getServiceManager();
        $config = $sm->get('config');

        if (false === $config['admin_layout']['use_admin_layout']) {
            return;
        }

        $match = $e->getRouteMatch();
        $controller = $e->getTarget();
        if (!$match instanceof RouteMatch || 0 !== strpos($match->getMatchedRouteName(), 'admin') || $controller->getEvent()->getResult()->terminate()
        ) {
            return;
        }
        //\Zend\Debug\Debug::dump(strpos($match->getMatchedRouteName(), 'admin/login'));
        if (0 === strpos($match->getMatchedRouteName(), 'admin/login')) {

            $layout = $config['admin_layout']['admin_login_layout_template'];
            $controller->layout($layout);
            return;
        }

        $layout = $config['admin_layout']['admin_layout_template'];
        $controller->layout($layout);
    }

    public function checkLogin($e) {

        $auth = $e->getApplication()->getServiceManager()->get("Zend\Authentication\AuthenticationService");
        $target = $e->getTarget();
        $match = $e->getRouteMatch();

        $controller = $match->getParam('controller');

        if (!in_array($controller, $this->whitelist)) {
            if (!$auth->hasIdentity()) {
                return $target->redirect()->toRoute('admin/login');
            }
        }
    }

    public function getViewHelperConfig() {
        return array(
            'factories' => array(
                'auth_helper' => function($sm) {
                    $helper = new AuthHelper;
                    return $helper;
                }
            )
        );
    }

    public function getServiceConfig() {

        return array(
            'factories' => array(
                'Admin\Model\CountryTable' => function($sm) {

                    $tableGateway = $sm->get('CountryTableGateway');

                    $table = new CountryTable($tableGateway);

                    return $table;
                },
                'CountryTableGateway' => function ($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $resultSetPrototype = new ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new Countries());

                    return new TableGateway('tbl_country', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\StateTable' => function($sm) {

                    $tableGateway = $sm->get('StateTableGateway');

                    $table = new StateTable($tableGateway);

                    return $table;
                },
                'StateTableGateway' => function ($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $resultSetPrototype = new ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new States());

                    return new TableGateway('tbl_state', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\CityTable' => function($sm) {

                    $tableGateway = $sm->get('CityTableGateway');

                    $table = new CityTable($tableGateway);

                    return $table;
                },
                'CityTableGateway' => function ($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $resultSetPrototype = new ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new Cities());

                    return new TableGateway('tbl_city', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\ReligionTable' => function($sm) {

                    $tableGateway = $sm->get('ReligionTableGateway');

                    $table = new ReligionTable($tableGateway);

                    return $table;
                },
                'ReligionTableGateway' => function ($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $resultSetPrototype = new ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new Religions());

                    return new TableGateway('tbl_religion', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\GothraTable' => function($sm) {

                    $tableGateway = $sm->get('GothraTableGateway');

                    $table = new GothraTable($tableGateway);

                    return $table;
                },
                'GothraTableGateway' => function ($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $resultSetPrototype = new ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new Gothras());

                    return new TableGateway('tbl_gothra_gothram', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\StarsignTable' => function($sm) {

                    $tableGateway = $sm->get('StarsignTableGateway');

                    $table = new StarsignTable($tableGateway);

                    return $table;
                },
                'StarsignTableGateway' => function ($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $resultSetPrototype = new ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new Starsigns());

                    return new TableGateway('tbl_star_sign', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\ZodiacsignTable' => function($sm) {

                    $tableGateway = $sm->get('ZodiacsignTableGateway');

                    $table = new ZodiacsignTable($tableGateway);

                    return $table;
                },
                'ZodiacsignTableGateway' => function ($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $resultSetPrototype = new ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new Zodiacsigns());

                    return new TableGateway('tbl_zodiac_sign_raasi', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\EducationfieldTable' => function($sm) {

                    $tableGateway = $sm->get('EducationfieldTableGateway');

                    $table = new EducationfieldTable($tableGateway);

                    return $table;
                },
                'EducationfieldTableGateway' => function ($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $resultSetPrototype = new ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new Educationfields());

                    return new TableGateway('tbl_education_field', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\EducationlevelTable' => function($sm) {

                    $tableGateway = $sm->get('EducationlevelTableGateway');

                    $table = new EducationlevelTable($tableGateway);

                    return $table;
                },
                'EducationlevelTableGateway' => function ($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $resultSetPrototype = new ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new Educationlevels());

                    return new TableGateway('tbl_education_level', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\ProfessionTable' => function($sm) {

                    $tableGateway = $sm->get('ProfessionTableGateway');

                    $table = new ProfessionTable($tableGateway);

                    return $table;
                },
                'ProfessionTableGateway' => function ($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $resultSetPrototype = new ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new Professions());

                    return new TableGateway('tbl_profession', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\DesignationTable' => function($sm) {

                    $tableGateway = $sm->get('DesignationTableGateway');

                    $table = new DesignationTable($tableGateway);

                    return $table;
                },
                'DesignationTableGateway' => function ($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $resultSetPrototype = new ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new Designations());

                    return new TableGateway('tbl_designation', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\UsertypeTable' => function($sm) {

                    $tableGateway = $sm->get('UsertypeTableGateway');

                    $table = new UsertypeTable($tableGateway);

                    return $table;
                },
                'UsertypeTableGateway' => function ($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $resultSetPrototype = new ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new Usertypes());

                    return new TableGateway('tbl_user_type', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\NewsTable' => function($sm) {

                    $tableGateway = $sm->get('NewsTableGateway');

                    $table = new NewsTable($tableGateway);

                    return $table;
                },
                'NewsTableGateway' => function ($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $resultSetPrototype = new ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new Newses());

                    return new TableGateway('tbl_news', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\NewscategoryTable' => function($sm) {

                    $tableGateway = $sm->get('NewscategoryTableGateway');

                    $table = new NewscategoryTable($tableGateway);

                    return $table;
                },
                'NewscategoryTableGateway' => function ($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $resultSetPrototype = new ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new Newscategories());

                    return new TableGateway('tbl_newscategory', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\UserinfoTable' => function($sm) {

                    $tableGateway = $sm->get('UserinfoTableGateway');

                    $table = new UserinfoTable($tableGateway);

                    return $table;
                },
                'UserinfoTableGateway' => function ($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $resultSetPrototype = new ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new Userinfos());

                    return new TableGateway('tbl_user_info', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\UserTable' => function($sm) {

                    $tableGateway = $sm->get('UserTableGateway');

                    $table = new UserTable($tableGateway);

                    return $table;
                },
                'UserTableGateway' => function ($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $resultSetPrototype = new ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new Users());

                    return new TableGateway('tbl_user', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\EventsTable' => function($sm) {

                    $tableGateway = $sm->get('EventsTableGateway');

                    $table = new EventsTable($tableGateway);

                    return $table;
                },
                'EventsTableGateway' => function ($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $resultSetPrototype = new ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new Events());

                    return new TableGateway('tbl_upcoming_events', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\AllCountryTable' => function($sm) {

                    $tableGateway = $sm->get('AllCountryTableGateway');

                    $table = new AllCountryTable($tableGateway);

                    return $table;
                },
                'AllCountryTableGateway' => function ($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $resultSetPrototype = new ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new AllCountries());

                    return new TableGateway('allcountries', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\PagesTable' => function($sm) {

                    $tableGateway = $sm->get('PagesTableGateway');

                    $table = new PagesTable($tableGateway);

                    return $table;
                },
                'PagesTableGateway' => function ($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $resultSetPrototype = new ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new AllPages());

                    return new TableGateway('tbl_static_pages', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\UsersRolesTable' => function($sm) {

                    $tableGateway = $sm->get('UsersRolesTableGateway');

                    $table = new UsersRolesTable($tableGateway);

                    return $table;
                },
                'UsersRolesTableGateway' => function ($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $resultSetPrototype = new ResultSet();

                    $resultSetPrototype->setArrayObjectPrototype(new UserRoles());

                    return new TableGateway('tbl_user_roles', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

}
