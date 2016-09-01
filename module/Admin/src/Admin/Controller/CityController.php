<?php

namespace Admin\Controller;

use Admin\Form\CityFilter;
use Admin\Form\CityForm;
use Admin\Model\Entity\Cities;
use Admin\Service\AdminServiceInterface;
use Common\Service\CommonServiceInterface;
use Zend\Db\Adapter\Adapter;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CityController extends AppController
{
    protected $data = array();
     protected $commonService;
    protected $adminService;

    public function __construct(CommonServiceInterface $commonService, AdminServiceInterface $adminService) {
        $this->commonService = $commonService;
        $this->adminService=$adminService;
    }
    
    public function indexAction()
    {   
         $cities = $this->getCityTable()->fetchAll($this->data);
         $stateNameList = $this->getStateTable()->customFields(array('id','state_name'));
		 $countryNameList = $this->getCountryTable()->customFields(array('id','country_name'));
        CityForm::$country_nameList = $countryNameList;
        CityForm::$state_nameList = $stateNameList;
		$form = new CityForm();
         $form->get('submit')->setAttribute('value', 'Add');
        return new ViewModel(array(
            'cities' => $cities,'form'=> $form, 'action'=>'index'));
    }

    public function AddAction()
    {
        $stateNameList = $this->getStateTable()->customFields(array('id','state_name'));

        CityForm::$state_nameList = $stateNameList;

        // echo"dssd"; die;

        $form = new CityForm();
        $form->get('submit')->setAttribute('value', 'Add');

        $request = $this->getRequest();
        if($request->isPost()){

            $cityEntity = new Cities();

               $form->setInputFilter(new CityFilter());
               $form->setData($request->getPost());
               
               if ($form->getInputFilter()->getValue('IsActive')==null) {
                $form->getInputFilter()->get('IsActive')->setRequired(false);
            }


               if($form->isValid()){

                $cityEntity->exchangeArray($form->getData());
                // print_r($cityEntity);die;
                $res = $this->getCityTable()->SaveCity($cityEntity);

//                     return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'city'
//                ));
                return new JsonModel(array("response" => $res));
               }else {

                foreach ($form->getmessages() as $key => $value) {
                    $errors[] = array("element" => $key, "errors" => $value['isEmpty']);
                }
                return new JsonModel(array("errors" => $errors, "FormId" => $_POST['FormId']));


            }
        }

        return new ViewModel(array('form'=> $form));
        
    }

    public function editAction()
    {
        $stateNameList = $this->getStateTable()->customFields(array('id','state_name'));

        CityForm::$state_nameList = $stateNameList;

        $form = new CityForm();
        if($this->params()->fromRoute('id')>0){
            $id = $this->params()->fromRoute('id');
            $city = $this->getCityTable()->getCity($id);
            // print_r($state);die;
            $form->bind($city);
            $form->get('submit')->setAttribute('value', 'Edit');
            // $this->editAction($form)
        }

        $request = $this->getRequest();
        if (!isset($_POST['chkedit'])) {
        if($request->isPost()){

            $cityEntity = new Cities();

               $form->setInputFilter(new CityFilter());
               $form->setData($request->getPost());
               
               if ($form->getInputFilter()->getValue('IsActive') == null) {
                    $form->getInputFilter()->get('IsActive')->setRequired(false);
                }


               if($form->isValid()){

                $cityEntity = $form->getData();
                    // print_r($countryEntity);die;
                    $res = $this->getCityTable()->SaveCity($cityEntity);

                    $response = $this->getResponse();
                    $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
                    $response->setContent(json_encode(array("response" => $res)));
                    return $response;
               }else {

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

    public function deleteAction()
    {
         
            $id = $this->params()->fromRoute('id');
            $state = $this->getCityTable()->deleteCity($id);
//            return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'city'
//                ));
            return $this->redirect()->toRoute('admin/city', array('action' => 'index'));
    }
    

    
    public function viewAction()
    {
        $id = $this->params()->fromRoute('id');

        $Info = $this->getCityTable()->getCityjoin($id);

        return new ViewModel(array('Info'=> $Info));
    }
    
    public function changestatusAction() {

        $data = (object) $_POST;
        $return = $this->getCityTable()->updatestatus($data);
        // print_r($return);
        return new JsonModel($return);
        exit();
    }
    
    public function viewByIdAction(){
        
        $id = $this->params()->fromRoute('id');

        //$Info = $this->getCountryTable()->getCountry($id);
        $info = $this->getCityTable()->getCityjoin($id);

        // echo"<pre>"; print_r($Info);die;
        $view=new ViewModel(array('info'=>$info));
        $view->setTerminal(true);
        return $view;
        
    }
    
    public function delmultipleAction() {
        $ids = $_POST['chkdata'];
        $result = $this->getCityTable()->delmultiple($ids);

        echo $result;
        exit();
    }
    
    public function statuschangeallAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $sql = "update tbl_city set IsActive=" . $_POST['val'] . " where id IN (" . $_POST['ids'] . ")";
        $results = $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        if ($results)
            echo "updated all";
        else
            echo "couldn't update";
        exit();
    }
    
    public function ajaxradiosearchAction() {
        $status = $_POST['IsActive'];
//       echo   "<pre>";
//       print_r($status);die;
       // $this->data = array("IsActive=$status");
        $this->data = $status;

        $cities = $this->getCityTable()->fetchAll2($this->data);
//        echo  "<pre>";
//        print_r($cities);die;
        // return new ViewModel(array('countries' => $countries));

        $view = new ViewModel(array('cities' => $cities));
        $view->setTemplate('admin/city/cityList');
        $view->setTerminal(true);
        return $view;
    }
   
}