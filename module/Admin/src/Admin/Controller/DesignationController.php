<?php

namespace Admin\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Admin\Model\Entity\Designations;
use Admin\Form\DesignationForm;
use Admin\Form\DesignationFilter;

class DesignationController extends AppController
{
    protected $data = array();
    
    public function indexAction()
    {   
         $designations = $this->getDesignationTable()->fetchAll($this->data);
            //echo   "<pre>";
          //print_r($designations);die;
         // print_r($cities);die;
		 
		   
		$form = new DesignationForm();
         $form->get('submit')->setAttribute('value', 'Add');

        return new ViewModel(array(
            'designations' => $designations,'form'=> $form, 'action'=>'index'));
			
    }

    public function AddAction()
    {
        $form = new DesignationForm();
        $form->get('submit')->setAttribute('value', 'Add');

        $request = $this->getRequest();
        if($request->isPost()){

            $designationEntity = new Designations();

               $form->setInputFilter(new DesignationFilter());
               $form->setData($request->getPost());


               if($form->isValid()){

                $designationEntity->exchangeArray($form->getData());
                // print_r($designationEntity);die;
                $res = $this->getDesignationTable()->SaveDesignation($designationEntity);

//                     return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'designation'
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
        $form = new DesignationForm();
        if($this->params()->fromRoute('id')>0){
            $id = $this->params()->fromRoute('id');
            // echo   $id;die;
            $designation = $this->getDesignationTable()->getDesignation($id);
            // print_r($designation);die;
            $form->bind($designation);
            $form->get('submit')->setAttribute('value', 'Edit');
            // $this->editAction($form)
        }

        $request = $this->getRequest();
        if (!isset($_POST['chkedit'])) {
        if($request->isPost()){

            $designationEntity = new Designations();

               $form->setInputFilter(new DesignationFilter());
               $form->setData($request->getPost());


               if($form->isValid()){

                $designationEntity = $form->getData();
                // print_r($cityEntity);die;
                $res = $this->getDesignationTable()->SaveDesignation($designationEntity);

//                     return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'designation'
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
            $designation = $this->getDesignationTable()->deleteDesignation($id);
//            return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'designation'
//                ));
            return $this->redirect()->toRoute('admin/designation', array('action' => 'index'));
    }
    
    public function viewAction()
    {
        $id = $this->params()->fromRoute('id');

        $Info = $this->getDesignationTable()->getDesignation($id);

        return new ViewModel(array('Info'=> $Info));
    }
    
    public function viewByIdAction(){
        
        $id = $this->params()->fromRoute('id');

        //$Info = $this->getCountryTable()->getCountry($id);
        $info = $this->getDesignationTable()->getDesignation($id);

        // echo"<pre>"; print_r($Info);die;
        $view=new ViewModel(array('info'=>$info));
        $view->setTerminal(true);
        return $view;
        
    }
    
    public function changestatusAction() {

        $data = (object) $_POST;
        $return = $this->getDesignationTable()->updatestatus($data);
        // print_r($return);
        return new JsonModel($return);
        exit();
    }
    
    public function delmultipleAction() {
        $ids = $_POST['chkdata'];
        $result = $this->getDesignationTable()->delmultiple($ids);

        echo $result;
        exit();
    }
    
    public function statuschangeallAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $sql = "update tbl_designation set IsActive=" . $_POST['val'] . " where id IN (" . $_POST['ids'] . ")";
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

        $designations = $this->getDesignationTable()->fetchAll($this->data);
        // return new ViewModel(array('countries' => $countries));

        $view = new ViewModel(array('designations' => $designations));
        $view->setTemplate('admin/designation/designationList');
        $view->setTerminal(true);
        return $view;
    }
    
    public function performsearchAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $field1 = empty($_POST['designation']) ? "" : "designation like '" . $_POST['designation'] . "%'";
        
        $sql = "select * from tbl_designation where " . $field1 . "";
       // $sql = rtrim($sql, "&&");
        $results = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        $view = new ViewModel(array("results" => $results));
        $view->setTerminal(true);
        return $view;


        exit();
    }
    
    public function designationsearchAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $data = $_POST['value'];
//        echo  "<pre>";
//        print_r($data);die;

        $result = $adapter->query("select * from tbl_designation where designation like '$data%' ", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);


        $view = new ViewModel(array("Results" => $result));
        $view->setTerminal(true);
        return $view;
        exit();
    }
   
}