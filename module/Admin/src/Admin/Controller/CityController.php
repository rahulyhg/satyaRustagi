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
    protected $data = '';//array();
     protected $commonService;
    protected $adminService;

    public function __construct(CommonServiceInterface $commonService, AdminServiceInterface $adminService) {
        $this->commonService = $commonService;
        $this->adminService=$adminService;
    }
    
    public function indexAction()
    {   //echo  "<pre>";echo "hello";exit;
//         $cities = $this->getCityTable()->fetchAll();
         $cities = $this->adminService->getCitiesList();
//         $stateNameList = $this->getStateTable()->customFields(array('id','state_name'));
         $stateNameList = $this->adminService->customFieldsState();
//		 $countryNameList = $this->getCountryTable()->customFields(array('id','country_name'));
                 $countryNameList = $this->adminService->customFields();
        CityForm::$country_nameList = $countryNameList;
        CityForm::$state_nameList = $stateNameList;
		$form = new CityForm();
         $form->get('submit')->setAttribute('value', 'Add');
         $filters_data = $this->getCountries();
        return new ViewModel(array(
            'cities' => $cities,'form'=> $form, 'action'=>'index', "filters_data" => $filters_data));
    }

    public function AddAction()
    {   //echo  "<pre>";
//                  echo  "hello";exit;
        //$stateNameList = $this->getStateTable()->customFields(array('id','state_name'));
        $stateNameList = $this->adminService->customFieldsState();
        
        CityForm::$state_nameList = $stateNameList;

        // echo"dssd"; die;

        $form = new CityForm();
        $form->get('submit')->setAttribute('value', 'Add');

        $request = $this->getRequest();
        if($request->isPost()){

            $cityEntity = new Cities();

               $form->setInputFilter(new CityFilter());
               $form->setData($request->getPost());
               
               if ($form->getInputFilter()->getValue('is_active')==null) {
                $form->getInputFilter()->get('is_active')->setRequired(false);
            }


               if($form->isValid()){

                //$cityEntity->exchangeArray($form->getData());
                // print_r($cityEntity);die;
                //$res = $this->getCityTable()->SaveCity($cityEntity);
//                   echo  "<pre>";
//                  echo  "hello";exit;
                $res= $this->adminService->SaveCity($form->getData());

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
        //$stateNameList = $this->getStateTable()->customFields(array('id','state_name'));
        $stateNameList = $this->adminService->customFieldsState();

        CityForm::$state_nameList = $stateNameList;

        $form = new CityForm();
        if($this->params()->fromRoute('id')>0){
            $id = $this->params()->fromRoute('id');
            //$city = $this->getCityTable()->getCity($id);
            $city= $this->adminService->getCity($id);
            // print_r($state);die;
            $form->bind($city);
            $form->get('submit')->setAttribute('value', 'Edit');
            // $this->editAction($form)
        }

        $request = $this->getRequest();
        if (!isset($_POST['chkedit'])) {
        if($request->isPost()){

            //$cityEntity = new Cities();

               $form->setInputFilter(new CityFilter());
               $form->setData($request->getPost());
               
//               if ($form->getInputFilter()->getValue('IsActive') == null) {
//                    $form->getInputFilter()->get('IsActive')->setRequired(false);
//                }


               if($form->isValid()){

                //$cityEntity = $form->getData();
                    // print_r($countryEntity);die;
                    //$res = $this->getCityTable()->SaveCity($cityEntity);
                    $res= $this->adminService->SaveCity($form->getData());

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
//            $state = $this->getCityTable()->deleteCity($id);
            $state= $this->adminService->delete('tbl_city', $id);
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

//        $data = (object) $_POST;
//        $return = $this->getCityTable()->updatestatus($data);
//        // print_r($return);
//        return new JsonModel($return);
//        exit();
        $request=$this->getRequest();
        $result= $this->adminService->changeStatus('tbl_city', $request->getPost('id'), $request->getPost());
        return new JsonModel($result);
    }
    
    public function viewByIdAction(){
        
        $id = $this->params()->fromRoute('id');

        //$Info = $this->getCountryTable()->getCountry($id);
        //$info = $this->getCityTable()->getCityjoin($id);
        $info = $this->adminService->viewByCityId('tbl_city', $id);

        // echo"<pre>"; print_r($Info);die;
        $view=new ViewModel(array('info'=>$info));
        $view->setTerminal(true);
        return $view;
        
    }
    
    public function delmultipleAction() {
        $ids = $_POST['chkdata'];
//        $result = $this->getCityTable()->delmultiple($ids);
        $result= $this->adminService->deleteMultiple('tbl_city', $ids);

        echo $result;
        exit();
    }
    
    public function changeStatusAllAction() {
//        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
//        $sql = "update tbl_city set IsActive=" . $_POST['val'] . " where id IN (" . $_POST['ids'] . ")";
//        $results = $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        $result= $this->adminService->changeStatusAll('tbl_city', $_POST['ids'], $_POST['val']);
//        return new JsonModel($result);
                if ($result)
            echo "updated all";
        else
            echo "couldn't update";
        exit();
    }
    
    public function ajaxradiosearchAction() {
        $status = $_POST['is_active'];
//       echo   "<pre>";
//       print_r($status);die;
       // $this->data = array("IsActive=$status");
        $this->data = $status;

        //$cities = $this->getCityTable()->fetchAll2($this->data);
        $cities = $this->adminService->getCityRadioList($_POST['is_active']);
//        echo  "<pre>";
//        print_r($cities);die;
        // return new ViewModel(array('countries' => $countries));

        $view = new ViewModel(array('cities' => $cities));
        $view->setTemplate('admin/city/cityList');
        $view->setTerminal(true);
        return $view;
    }
    
    //added by amir
    
    public function getCountries() {
//        echo  "<pre>";
//        echo "hello";exit;
//        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
//        $filters_array = array("country" => "tbl_country", "profession" => "tbl_profession", "city" => "tbl_city"
//            , "state" => "tbl_state", "education_level" => "tbl_education_field", "designation" => "tbl_designation"
//            , "height" => "tbl_height");
//        foreach ($filters_array as $key => $table) {
//            $filters_data[$key] = $adapter->query("select * from " . $table . "", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
//        }
        
        $filters_data = $this->adminService->getCountriesList();
//        \Zend\Debug\Debug::dump($filters_data);
//        exit;
        return $filters_data;
    }
    
    /*     * ****Ajax Call***** */
    
    public function getStateNameAction() {
//        echo  "<pre>";
//        print_r($_POST['Country_ID']);exit;
//           print_r($Request->getPost("Country_ID"));die;
//        $Request = $this->getRequest();
//        if ($Request->isPost()) {
            $Country_ID = $_POST['Country_ID'];
//            $state_name = $this->getStateTable()->getStateListByCountryCode($Country_ID);
            $state_name = $this->adminService->getStateListByCountryCode($Country_ID);
//            print_r($state_name);
//            exit;
//            echo  "<pre>";
//            \Zend\Debug\Debug::dump($filters_data);exit;
//        }
        if (count($state_name))
                return new JsonModel(array("Status" => "Success", "statelist" => $state_name));
            else
                return new JsonModel(array("Status" => "Failed", "statelist" => NULL));
            
    }

    /*     * ****Ajax Call***** */

    public function getCityNameAction() {
//        $Request = $this->getRequest();
//        if ($Request->isPost()) {
//            $State_ID = $Request->getPost("State_ID");
            $State_ID = $_POST['State_ID'];
//            $city_name = $this->getCityTable()->getCityListByStateCode($State_ID);
            $city_name = $this->adminService->getCityListByStateCode($State_ID);
            if (count($city_name))
                return new JsonModel(array("Status" => "Success", "statelist" => $city_name));
            else
                return new JsonModel(array("Status" => "Failed", "statelist" => NULL));
       // }
    }
    
     public function performsearchAction() {      
//         echo   "hello";exit;
//        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        if(isset($_POST['Country_id']) && !isset($_POST['State_id'])){
//             $sql = "SELECT * FROM tbl_state WHERE country_id=".$_POST['Country_id']."";
//            $results = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
//            $resultSet=new \Zend\Db\ResultSet\ResultSet();
//            $resultSet->initialize($results);
           //return $results;
//            $i=0;
//            $data = array();
//            foreach($results as $result){
//               
//               $data[$i] = $result->id;
//               
//                $i++;
//            }
//            
//             $states_id = implode(',',$data);
            //echo  "<pre>";
            //print_r($data2);
            
            //exit;


//            $sql2 = "SELECT tbl_city.*,tbl_state.state_name FROM tbl_city INNER JOIN tbl_state ON "
//                    . "tbl_city.state_id=tbl_state.id where tbl_city.state_id IN($states_id)";
//            $results1 = $adapter->query($sql2, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
            $results1 = $this->adminService->getCityListByCountry($_POST['Country_id']);
//            echo  "<pre>";print_r($results);
//            exit;

            $view = new ViewModel(array("results" => $results1));
            $view->setTerminal(true);
            return $view;
            
        }
        
        if(isset($_POST['Country_id']) && isset($_POST['State_id']) && !isset($_POST['City_id'])){
            
//            $sql3 = "SELECT tbl_city.*,tbl_state.state_name FROM tbl_city INNER JOIN tbl_state ON "
//                    . "tbl_city.state_id=tbl_state.id where tbl_city.state_id=".$_POST['State_id']."";
//            $results2 = $adapter->query($sql3, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
            $results2 = $this->adminService->getCityListByState($_POST['State_id']);
//            echo  "<pre>";print_r($results);
//            exit;

            $view = new ViewModel(array("results" => $results2));
            $view->setTerminal(true);
            return $view;
            
        }
        
        if(isset($_POST['Country_id']) && isset($_POST['State_id']) && isset($_POST['City_id'])){
            
            $sql4 = "SELECT tbl_city.*,tbl_state.state_name FROM tbl_city INNER JOIN tbl_state ON "
                    . "tbl_city.state_id=tbl_state.id where tbl_city.id=".$_POST['City_id']."";
//            $results3 = $adapter->query($sql4, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
            $results3 = $this->adminService->getCityListByCity($_POST['City_id']);
//            echo  "<pre>";print_r($results);
//            exit;

            $view = new ViewModel(array("results" => $results3));
            $view->setTerminal(true);
            return $view;
            
        }
        
        
       
    }
   
}