<?php

namespace Admin\Controller;

// use Zend\Mvc\Controller\AbstractActionController;


use Admin\Form\CountryFilter;
use Admin\Form\CountryForm;
use Admin\Model\Entity\Countries;
use Admin\Service\AdminServiceInterface;
use Common\Service\CommonServiceInterface;
use Zend\Db\Adapter\Adapter;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CountryController extends AppController {

    protected $data = '';//array();
     protected $commonService;
    protected $adminService;

    public function __construct(CommonServiceInterface $commonService, AdminServiceInterface $adminService) {
        $this->commonService = $commonService;
        $this->adminService=$adminService;
    }

    public function indexAction() {
        //$countries = $this->getCountryTable()->fetchAll($this->data);
        $countries = $this->adminService->getCountriesList($this->data);

        $action = $this->getRequest()->getUri() . "/searchboxresults";
        CountryForm::$actionName = $action;
        $form = new CountryForm();
        $form->get('submit')->setAttribute('value', 'Add');


        return new ViewModel(array(
            'countries' => $countries, 'form' => $form, 'action'=>'index'));
    }

    public function AddAction() {


        $form = new CountryForm();
        if ($this->params()->fromRoute('id') > 0) {
            $id = $this->params()->fromRoute('id');
            $country = $this->getCountryTable()->getCountry($id);
            // print_r($country);die;
            $form->bind($country);
            $form->get('submit')->setAttribute('value', 'Edit');
            // $this->editAction($form)
        } else{
            $form->get('submit')->setAttribute('value', 'Add');
        }
        
        $request = $this->getRequest();
        //return $request;
        //exit;
        if ($request->isPost()) {

//            $countryEntity = new Countries();
//
            $form->setInputFilter(new CountryFilter());
            $form->setData($request->getPost());
            
            if ($form->getInputFilter()->getValue('is_active')==null) {
                $form->getInputFilter()->get('is_active')->setRequired(false);
            }


            if ($form->isValid()) {

                //$countryEntity->exchangeArray($form->getData());
//                $res = $this->getCountryTable()->SaveCountry($countryEntity);
                $res= $this->adminService->SaveCountry($form->getData());
                //$response = $this->getResponse();
                //$response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
                //$response->setContent(json_encode(array("response" => $res)));
                //return $response;
                return new JsonModel(array("response" => $res));
               
            } else {

                foreach ($form->getmessages() as $key => $value) {
                    $errors[] = array("element" => $key, "errors" => $value['isEmpty']);
                }
                return new JsonModel(array("errors" => $errors, "FormId" => $_POST['FormId']));

//                $response = $this->getResponse();
//                $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
//                $response->setContent(json_encode(array("errors" => $errors, "FormId" => $_POST['FormId'])));
//                return $response;
            }
        }

        return new ViewModel(array('form' => $form));
    }

    public function editAction() {

        $action = $this->url()->fromRoute('admin/country', array('action' => 'searchboxresults'));




        CountryForm::$actionName = $action;
        $form = new CountryForm();
        if ($this->params()->fromRoute('id') > 0) {
//            echo  "<pre>";echo   "hello";exit;
            $id = $this->params()->fromRoute('id');
            //$country = $this->getCountryTable()->getCountry($id);
            $country= $this->adminService->getCountry($id);
//             echo  "<pre>";
//            print_r($country);exit;
            $form->bind($country);
            $form->get('submit')->setAttribute('value', 'Edit');
            // $this->editAction($form)
        }

        $request = $this->getRequest();
        if (!isset($_POST['chkedit'])) {
            if ($request->isPost()) {

//                $countryEntity = new Countries();
//
                $form->setInputFilter(new CountryFilter());
                $form->setData($request->getPost());

                if ($form->getInputFilter()->getValue('is_active') == null) {
                    $form->getInputFilter()->get('is_active')->setRequired(false);
                }

                if ($form->isValid()) {

                    //$countryEntity = $form->getData();
                    // print_r($countryEntity);die;
                    //$res = $this->getCountryTable()->SaveCountry($countryEntity);
                    $res= $this->adminService->saveCountry($form->getData());

                    $response = $this->getResponse();
                    $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
                    $response->setContent(json_encode(array("response" => $res)));
                    return $response;
                } else {

                    foreach ($form->getmessages() as $key => $value) {
                        $errors[] = array("element" => $key, "errors" => $value['isEmpty']);
                    }

                    $response = $this->getResponse();
                    $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
                    $response->setContent(json_encode(array("errors", $errors)));
                    return $response;
                }
            }
        }


        $view = new ViewModel(array('form' => $form, 'id' => $id));
        $view->setTerminal(true);
        return $view;
    }

    public function deleteAction() {

//        $id = $this->params()->fromRoute('id');
//        // print_r($id);
//        $country = $this->getCountryTable()->deleteCountry($id);
//        return $this->redirect()->toRoute('admin/country', array('action' => 'index'));
        $id = $this->params()->fromRoute('id');
            //print_r($id);exit;
            //$education = $this->getEducationfieldTable()->deleteEducationField($id);
            $result= $this->adminService->delete('tbl_country', $id);
//            return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'religion'
//                ));
            return $this->redirect()->toRoute('admin/country', array('action' => 'index'));
    }

    public function viewAction() {
        $id = $this->params()->fromRoute('id');

        $Info = $this->getCountryTable()->getCountry($id);

        // echo"<pre>"; print_r($Info);die;

        return new JsonModel(array('Info' => $Info));
    }
    
    public function viewByIdAction(){
        
//        $id = $this->params()->fromRoute('id');
//
//        //$Info = $this->getCountryTable()->getCountry($id);
//        $info = $this->getCountryTable()->getCountry($id);
//
//        // echo"<pre>"; print_r($Info);die;
//        $view=new ViewModel(array('info'=>$info));
//        $view->setTerminal(true);
//        return $view;
        $id = $this->params()->fromRoute('id');
//        echo   "<pre>";
//        print_r($id);exit;
        //$Info = $this->getCountryTable()->getCountry($id);
        //$info = $this->getEducationfieldTable()->getEducationfield($id);
          $info = $this->adminService->viewByCountryId('tbl_country', $id);
          //\Zend\Debug\Debug::dump($info);
         //echo"<pre>"; print_r($info);die;
        $view=new ViewModel(array('info'=>$info));
        $view->setTerminal(true);
        return $view;
        
    }

    public function searchboxresultsAction() {
        $data = $_POST['value'];
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $result = $adapter->query("select * from allcountries where (id not in (select master_country_id
            from tbl_country) && country_name like '$data%') ", Adapter::QUERY_MODE_EXECUTE);

        // $result = $this->getAllCountryTable()->searchresults($data);
        $view = new ViewModel(array("countries" => $result, "parentname" => $_POST['field']));
        $view->setTerminal(true);
        return $view;
        exit();
    }

    public function countrysearchAction() {
//        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $data = $_POST['value'];

//        $result = $adapter->query("select * from tbl_country where country_name like '$data%' ", Adapter::QUERY_MODE_EXECUTE);
        $result = $this->adminService->countrySearch($data);
//        echo   "<pre>";
//                print_r($result);exit;

        $view = new ViewModel(array("Results" => $result));
        $view->setTerminal(true);
        return $view;
        exit();
    }

    public function performsearchAction() {
//        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
//
//        $field1 = empty($_POST['country_name']) ? "" : "country_name like '" . $_POST['country_name'] . "%' &&";
//        $field2 = empty($_POST['country_code']) ? "" : "country_code like '" . $_POST['country_code'] . "%' &&";
//        $field3 = empty($_POST['dial_code']) ? "" : "dial_code like '" . $_POST['dial_code'] . "%' ";
            
//        $sql = "select * from tbl_country where " . $field1 . $field2 . $field3 . "";
          $results = $this->adminService->performSearchCountry($_POST['country_name'],$_POST['country_code'],$_POST['dial_code']);
        
//        $sql = rtrim($sql, "&&");
//        $results = $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        $view = new ViewModel(array("results" => $results));
        $view->setTerminal(true);
        return $view;


        exit();
    }

    public function ajaxradiosearchAction() {
//        $status = $_POST['IsActive'];
//        $this->data = array("IsActive=$status");
//
//        $countries = $this->getCountryTable()->fetchAll($this->data);
//        // return new ViewModel(array('countries' => $countries));
//
//        $view = new ViewModel(array('countries' => $countries));
//        $view->setTemplate('admin/country/countryList');
//        $view->setTerminal(true);
//        return $view;
        
         $status = $_POST['is_active'];
//        echo  "<pre>";
//        print_r($status);exit;
        //$this->data = array("IsActive=$status");
        $this->data = $status;
//        Debug::dump($this->data);
//        exit;
        //$educations = $this->getEducationfieldTable()->fetchAll($this->data);
        $countries = $this->adminService->getCountryRadioList($_POST['is_active']);
        // return new ViewModel(array('countries' => $countries));

        $view = new ViewModel(array('countries' => $countries));
        $view->setTemplate('admin/country/countryList');
        $view->setTerminal(true);
        return $view;
    }

    public function changestatusAction() {

        //$data = (object) $_POST;
        //$return = $this->getCountryTable()->updatestatus($data);
        // print_r($return);
        //return new JsonModel($return);
        //exit();
        
        //        echo   "<pre>";
//        print_r('hello');exit;
        $request=$this->getRequest();
//print_r($request->getPost());
//exit;
        
       $result= $this->adminService->changeStatus('tbl_country', $request->getPost('id'), $request->getPost());

//        $data = (object) $_POST;
//        $return = $this->getEducationfieldTable()->updatestatus($data);
//        // print_r($return);
        return new JsonModel($result);
//        exit();
    }

    public function delmultipleAction() {
//        $ids = $_POST['chkdata'];
//        $result = $this->getCountryTable()->delmultiple($ids);
//
//        echo $result;
//        exit();
         $ids = $_POST['chkdata'];
//       echo   "<pre>";
//        print_r($ids);exit;
        //$result = $this->getEducationfieldTable()->delmultiple($ids);
        $result= $this->adminService->deleteMultiple('tbl_country', $ids);

        echo $result;
        exit();
    }

    public function changeStatusAllAction() {
//        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
//        $sql = "update tbl_country set IsActive=" . $_POST['val'] . " where id IN (" . $_POST['ids'] . ")";
//        $results = $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
//        if ($results)
//            echo "updated all";
//        else
//            echo "couldn't update";
//        exit();
        $result= $this->adminService->changeStatusAll('tbl_country', $_POST['ids'], $_POST['val']);
        
        //return new JsonModel($result);
                if ($result)
            echo "updated all";
        else
            echo "couldn't update";
        exit();
    }

}
