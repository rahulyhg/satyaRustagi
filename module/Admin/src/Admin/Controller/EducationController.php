<?php

namespace Admin\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Admin\Model\Entity\Educationfields;
use Admin\Model\Entity\Educationlevels;
use Admin\Form\EducationfieldForm;
use Admin\Form\EducationlevelForm;
use Admin\Form\EducationfieldFilter;
use Admin\Form\EducationlevelFilter;

class EducationController extends AppController
{
    protected $data = array();
    
    public function indexAction()
    {   
         $educations = $this->getEducationfieldTable()->fetchAll($this->data);
            //echo   "<pre>";
          //print_r($religions);die;
         // print_r($cities);die;
		 
		   
		$form = new EducationfieldForm();
         $form->get('submit')->setAttribute('value', 'Add');

        return new ViewModel(array(
            'educations' => $educations,'form'=> $form, 'action'=>'index'));
			
    }

    public function AddAction()
    {
        $form = new EducationfieldForm();
        $form->get('submit')->setAttribute('value', 'Add');

        $request = $this->getRequest();
        if($request->isPost()){

            $educationfieldEntity = new Educationfields();

               $form->setInputFilter(new EducationfieldFilter());
               $form->setData($request->getPost());


               if($form->isValid()){

                $educationfieldEntity->exchangeArray($form->getData());
                // print_r($religionEntity);die;
                $res = $this->getEducationfieldTable()->SaveEducationfield($educationfieldEntity);

//                     return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'religion'
//                ));
                return new JsonModel(array("response" => $res));
               } else {

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
        $form = new EducationfieldForm();
        if($this->params()->fromRoute('id')>0){
            $id = $this->params()->fromRoute('id');
            // echo   $id;die;
            $education = $this->getEducationfieldTable()->getEducationfield($id);
            // print_r($religion);die;
            $form->bind($education);
            $form->get('submit')->setAttribute('value', 'Edit');
            // $this->editAction($form)
        }

        $request = $this->getRequest();
        if (!isset($_POST['chkedit'])) {
        if($request->isPost()){

            $educationfieldEntity = new Educationfields();

               $form->setInputFilter(new EducationfieldFilter());
               $form->setData($request->getPost());


               if($form->isValid()){

                $educationfieldEntity = $form->getData();
                // print_r($cityEntity);die;
                $res = $this->getEducationfieldTable()->SaveEducationfield($educationfieldEntity);

//                     return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'religion'
//                ));
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

        //return new ViewModel(array('form'=> $form,'id'=>$id));
        $view = new ViewModel(array('form' => $form, 'id' => $id));
        $view->setTerminal(true);
        return $view;

    }

    public function deleteAction()
    {
         
            $id = $this->params()->fromRoute('id');
            $education = $this->getEducationfieldTable()->deleteEducationField($id);
//            return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'religion'
//                ));
            return $this->redirect()->toRoute('admin/education', array('action' => 'index'));
    }
    
    public function viewAction()
    {
        $id = $this->params()->fromRoute('id');

        $Info = $this->getEducationfieldTable()->getEducationfield($id);//getReligion

        return new ViewModel(array('Info'=> $Info));
    }
    
    public function viewByIdAction(){
        
        $id = $this->params()->fromRoute('id');

        //$Info = $this->getCountryTable()->getCountry($id);
        $info = $this->getEducationfieldTable()->getEducationfield($id);

        // echo"<pre>"; print_r($Info);die;
        $view=new ViewModel(array('info'=>$info));
        $view->setTerminal(true);
        return $view;
        
    }
    
    public function changestatusAction() {

        $data = (object) $_POST;
        $return = $this->getEducationfieldTable()->updatestatus($data);
        // print_r($return);
        return new JsonModel($return);
        exit();
    }
    
    public function delmultipleAction() {
        $ids = $_POST['chkdata'];
        $result = $this->getEducationfieldTable()->delmultiple($ids);

        echo $result;
        exit();
    }
    
    public function statuschangeallAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $sql = "update tbl_education_field set IsActive=" . $_POST['val'] . " where id IN (" . $_POST['ids'] . ")";
        $results = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        if ($results)
            echo "updated all";
        else
            echo "couldn't update";
        exit();
    }
    
    public function ajaxradiosearchAction() {
        $status = $_POST['IsActive'];
        $this->data = array("IsActive=$status");

        $educations = $this->getEducationfieldTable()->fetchAll($this->data);
        // return new ViewModel(array('countries' => $countries));

        $view = new ViewModel(array('educations' => $educations));
        $view->setTemplate('admin/education/educationfieldList');
        $view->setTerminal(true);
        return $view;
    }
    
    public function performsearchAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $field1 = empty($_POST['education_field']) ? "" : "education_field like '" . $_POST['education_field'] . "%'";
        
        $sql = "select * from tbl_education_field where " . $field1 . "";
       // $sql = rtrim($sql, "&&");
        $results = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        $view = new ViewModel(array("results" => $results));
        $view->setTerminal(true);
        return $view;


        exit();
    }
    
    public function educationfieldsearchAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $data = $_POST['value'];
//        echo  "<pre>";
//        print_r($data);die;

        $result = $adapter->query("select * from tbl_education_field where education_field like '$data%' ", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);


        $view = new ViewModel(array("Results" => $result));
        $view->setTerminal(true);
        return $view;
        exit();
    }
    
    //Education Level Part added by amir
    
    public function manageEducationLevelAction()
    {   
         $educations = $this->getEducationlevelTable()->fetchAll($this->data);
            //echo   "<pre>";
          //print_r($educations);die;
         // print_r($cities);die;
		 
		   
		$form = new EducationlevelForm();
         $form->get('submit')->setAttribute('value', 'Add');
        
        return new ViewModel(array(
            'educations' => $educations,'form'=> $form, 'action'=>'manageEducationLevel'));
         //echo    "hi";die;
			
    }

    public function AddeduLevelAction()
    {
        $form = new EducationlevelForm();
        $form->get('submit')->setAttribute('value', 'Add');

        $request = $this->getRequest();
        if($request->isPost()){

            $educationlevelEntity = new Educationlevels();

               $form->setInputFilter(new EducationlevelFilter());
               $form->setData($request->getPost());


               if($form->isValid()){

                $educationlevelEntity->exchangeArray($form->getData());
                // print_r($religionEntity);die;
                $res = $this->getEducationlevelTable()->SaveEducationlevel($educationlevelEntity);

//                     return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'religion'
//                ));
                return new JsonModel(array("response" => $res));
               } else {

                foreach ($form->getmessages() as $key => $value) {
                    $errors[] = array("element" => $key, "errors" => $value['isEmpty']);
                }
                return new JsonModel(array("errors" => $errors, "FormId" => $_POST['FormId']));
            }
        }

        return new ViewModel(array('form'=> $form));
        
    }

    public function editeduLevelAction()
    {
        $form = new EducationlevelForm();
        if($this->params()->fromRoute('id')>0){
            $id = $this->params()->fromRoute('id');
            // echo   $id;die;
            $education = $this->getEducationlevelTable()->getEducationlevel($id);
            // print_r($religion);die;
            $form->bind($education);
            $form->get('submit')->setAttribute('value', 'Edit');
            // $this->editAction($form)
        }

        $request = $this->getRequest();
        if (!isset($_POST['chkedit'])) {
        if($request->isPost()){

            $educationlevelEntity = new Educationlevels();

               $form->setInputFilter(new EducationlevelFilter());
               $form->setData($request->getPost());


               if($form->isValid()){

                $educationlevelEntity = $form->getData();
                // print_r($cityEntity);die;
                $res = $this->getEducationlevelTable()->SaveEducationlevel($educationlevelEntity);

//                     return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'religion'
//                ));
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

        //return new ViewModel(array('form'=> $form,'id'=>$id));
        $view = new ViewModel(array('form' => $form, 'id' => $id));
        $view->setTerminal(true);
        return $view;

    }

    public function deleteeduLevelAction()
    {
         
            $id = $this->params()->fromRoute('id');
            $education = $this->getEducationlevelTable()->deleteEducationLevel($id);
//            return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'religion'
//                ));
            return $this->redirect()->toRoute('admin/education', array('action' => 'manageEducationLevel'));
    }
    
    public function vieweduLevelAction()
    {
        $id = $this->params()->fromRoute('id');

        $Info = $this->getEducationlevelTable()->getEducationlevel($id);//getReligion

        return new ViewModel(array('Info'=> $Info));
    }
    
    public function viewByIdeduLevelAction(){
        
        $id = $this->params()->fromRoute('id');

        //$Info = $this->getCountryTable()->getCountry($id);
        $info = $this->getEducationlevelTable()->getEducationlevel($id);

        // echo"<pre>"; print_r($Info);die;
        $view=new ViewModel(array('info'=>$info));
        $view->setTerminal(true);
        return $view;
        
    }
    
    public function changestatuseduLevelAction() {

        $data = (object) $_POST;
        $return = $this->getEducationlevelTable()->updatestatus($data);
        // print_r($return);
        return new JsonModel($return);
        exit();
    }
    
    public function delmultipleeduLevelAction() {
        $ids = $_POST['chkdata'];
        $result = $this->getEducationlevelTable()->delmultiple($ids);

        echo $result;
        exit();
    }
    
    public function statuschangealleduLevelAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $sql = "update tbl_education_level set IsActive=" . $_POST['val'] . " where id IN (" . $_POST['ids'] . ")";
        $results = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        if ($results)
            echo "updated all";
        else
            echo "couldn't update";
        exit();
    }
    
    public function ajaxradiosearcheduLevelAction() {
        $status = $_POST['IsActive'];
        $this->data = array("IsActive=$status");

        $educations = $this->getEducationlevelTable()->fetchAll($this->data);
        // return new ViewModel(array('countries' => $countries));

        $view = new ViewModel(array('educations' => $educations));
        $view->setTemplate('admin/education/educationlevelList');
        $view->setTerminal(true);
        return $view;
    }
    
    public function performsearchlevelAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $field1 = empty($_POST['education_level']) ? "" : "education_level like '" . $_POST['education_level'] . "%'";
        
        $sql = "select * from tbl_education_level where " . $field1 . "";
       // $sql = rtrim($sql, "&&");
        $results = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        $view = new ViewModel(array("results" => $results));
        $view->setTerminal(true);
        return $view;


        exit();
    }
    
    public function educationlevelsearchAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $data = $_POST['value'];
//       echo  "<pre>";
//       print_r($data);die;

        $result = $adapter->query("select * from tbl_education_level where education_level like '$data%' ", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);


        $view = new ViewModel(array("Results" => $result));
        $view->setTerminal(true);
        return $view;
        exit();
    }
    
    
   
}