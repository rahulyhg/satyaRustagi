<?php

namespace Admin\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Admin\Model\Entity\Religions;
use Admin\Form\ReligionForm;
use Admin\Form\ReligionFilter;

class ReligionController extends AppController
{
    protected $data = array();
    
    public function indexAction()
    {   
         $religions = $this->getReligionTable()->fetchAll($this->data);
            //echo   "<pre>";
          //print_r($religions);die;
         // print_r($cities);die;
		 
		   
		$form = new ReligionForm();
         $form->get('submit')->setAttribute('value', 'Add');

        return new ViewModel(array(
            'religions' => $religions,'form'=> $form, 'action'=>'index'));
			
    }

    public function AddAction()
    {
        $form = new ReligionForm();
        $form->get('submit')->setAttribute('value', 'Add');

        $request = $this->getRequest();
        if($request->isPost()){

            $religionEntity = new Religions();

               $form->setInputFilter(new ReligionFilter());
               $form->setData($request->getPost());


               if($form->isValid()){

                $religionEntity->exchangeArray($form->getData());
                // print_r($religionEntity);die;
                $res = $this->getReligionTable()->SaveReligion($religionEntity);

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
        $form = new ReligionForm();
        if($this->params()->fromRoute('id')>0){
            $id = $this->params()->fromRoute('id');
            // echo   $id;die;
            $religion = $this->getReligionTable()->getReligion($id);
            // print_r($religion);die;
            $form->bind($religion);
            $form->get('submit')->setAttribute('value', 'Edit');
            // $this->editAction($form)
        }

        $request = $this->getRequest();
        if (!isset($_POST['chkedit'])) {
        if($request->isPost()){

            $religionEntity = new Religions();

               $form->setInputFilter(new ReligionFilter());
               $form->setData($request->getPost());


               if($form->isValid()){

                $religionEntity = $form->getData();
                // print_r($cityEntity);die;
                $res = $this->getReligionTable()->SaveReligion($religionEntity);

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
            $religion = $this->getReligionTable()->deleteReligion($id);
//            return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'religion'
//                ));
            return $this->redirect()->toRoute('admin/religion', array('action' => 'index'));
    }
    
    public function viewAction()
    {
        $id = $this->params()->fromRoute('id');

        $Info = $this->getReligionTable()->getReligion($id);

        return new ViewModel(array('Info'=> $Info));
    }
    
    public function viewByIdAction(){
        
        $id = $this->params()->fromRoute('id');

        //$Info = $this->getCountryTable()->getCountry($id);
        $info = $this->getReligionTable()->getReligion($id);

        // echo"<pre>"; print_r($Info);die;
        $view=new ViewModel(array('info'=>$info));
        $view->setTerminal(true);
        return $view;
        
    }
    
    public function changestatusAction() {

        $data = (object) $_POST;
        $return = $this->getReligionTable()->updatestatus($data);
        // print_r($return);
        return new JsonModel($return);
        exit();
    }
    
    public function delmultipleAction() {
        $ids = $_POST['chkdata'];
        $result = $this->getReligionTable()->delmultiple($ids);

        echo $result;
        exit();
    }
    
    public function statuschangeallAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $sql = "update tbl_religion set IsActive=" . $_POST['val'] . " where id IN (" . $_POST['ids'] . ")";
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

        $religions = $this->getReligionTable()->fetchAll($this->data);
        // return new ViewModel(array('countries' => $countries));

        $view = new ViewModel(array('religions' => $religions));
        $view->setTemplate('admin/religion/religionList');
        $view->setTerminal(true);
        return $view;
    }
    
    public function performsearchAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $field1 = empty($_POST['religion_name']) ? "" : "religion_name like '" . $_POST['religion_name'] . "%'";
        
        $sql = "select * from tbl_religion where " . $field1 . "";
       // $sql = rtrim($sql, "&&");
        $results = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        $view = new ViewModel(array("results" => $results));
        $view->setTerminal(true);
        return $view;


        exit();
    }
    
    public function religionsearchAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $data = $_POST['value'];
//        echo  "<pre>";
//        print_r($data);die;

        $result = $adapter->query("select * from tbl_religion where religion_name like '$data%' ", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);


        $view = new ViewModel(array("Results" => $result));
        $view->setTerminal(true);
        return $view;
        exit();
    }
   
}