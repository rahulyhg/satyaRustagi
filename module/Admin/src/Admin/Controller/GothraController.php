<?php

namespace Admin\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Admin\Model\Entity\Gothras;
use Admin\Form\GothraForm;
use Admin\Form\GothraFilter;

class GothraController extends AppController
{   
    protected $data = array();
    
    public function indexAction()
    {   
        
         $gothras = $this->getGothraTable()->fetchAll($this->data);
//            echo   "<pre>";
//          print_r($gothras);die;
		 
		 $form = new GothraForm();
         $form->get('submit')->setAttribute('value', 'Add');

        return new ViewModel(array(
            'gothras' => $gothras,'form'=> $form, 'action'=>'index'));
			

       // return new ViewModel(array(
         //   'gothras' => $gothras));
    }

    public function AddAction()
    {
        $form = new GothraForm();
        $form->get('submit')->setAttribute('value', 'Add');

        $request = $this->getRequest();
        if($request->isPost()){

            $gothraEntity = new Gothras();

               $form->setInputFilter(new GothraFilter());
               $form->setData($request->getPost());


               if($form->isValid()){

                $gothraEntity->exchangeArray($form->getData());
                // print_r($religionEntity);die;
                $res = $this->getGothraTable()->SaveGothra($gothraEntity);

//                     return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'gothra'
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
        

        $form = new GothraForm();
        if($this->params()->fromRoute('id')>0){
            $id = $this->params()->fromRoute('id');
            // echo   $id;die;
            $gothra = $this->getGothraTable()->getGothra($id);
            // print_r($religion);die;
            $form->bind($gothra);
            $form->get('submit')->setAttribute('value', 'Edit');
            // $this->editAction($form)
        }

        $request = $this->getRequest();
        if (!isset($_POST['chkedit'])) {
        if($request->isPost()){

            $gothraEntity = new Gothras();

               $form->setInputFilter(new GothraFilter());
               $form->setData($request->getPost());


               if($form->isValid()){

                $gothraEntity = $form->getData();
                // print_r($cityEntity);die;
                $res = $this->getGothraTable()->SaveGothra($gothraEntity);
//
//                     return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'gothra'
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
            $gothra = $this->getGothraTable()->deleteGothra($id);
//            return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'gothra'
//                ));
            return $this->redirect()->toRoute('admin/gothra', array('action' => 'index'));
    }
    
    public function viewAction()
    {
        $id = $this->params()->fromRoute('id');

        $Info = $this->getGothraTable()->getGothra($id);

        return new ViewModel(array('Info'=> $Info));
    }
    
    public function viewByIdAction(){
        
        $id = $this->params()->fromRoute('id');

        //$Info = $this->getCountryTable()->getCountry($id);
        $info = $this->getGothraTable()->getGothra($id);

        // echo"<pre>"; print_r($Info);die;
        $view=new ViewModel(array('info'=>$info));
        $view->setTerminal(true);
        return $view;
        
    }
    
    public function changestatusAction() {

        $data = (object) $_POST;
        $return = $this->getGothraTable()->updatestatus($data);
        // print_r($return);
        return new JsonModel($return);
        exit();
    }
    
    public function delmultipleAction() {
        $ids = $_POST['chkdata'];
        $result = $this->getGothraTable()->delmultiple($ids);

        echo $result;
        exit();
    }
    
    public function statuschangeallAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $sql = "update tbl_gothra_gothram set IsActive=" . $_POST['val'] . " where id IN (" . $_POST['ids'] . ")";
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

        $gothras = $this->getGothraTable()->fetchAll($this->data);
        // return new ViewModel(array('countries' => $countries));

        $view = new ViewModel(array('gothras' => $gothras));
        $view->setTemplate('admin/gothra/gothraList');
        $view->setTerminal(true);
        return $view;
    }
    
    public function performsearchAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $field1 = empty($_POST['gothra_name']) ? "" : "gothra_name like '" . $_POST['gothra_name'] . "%'";
        
        $sql = "select * from tbl_gothra_gothram where " . $field1 . "";
       // $sql = rtrim($sql, "&&");
        $results = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        $view = new ViewModel(array("results" => $results));
        $view->setTerminal(true);
        return $view;


        exit();
    }
    
    public function gothrasearchAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $data = $_POST['value'];
//        echo  "<pre>";
//        print_r($data);die;

        $result = $adapter->query("select * from tbl_gothra_gothram where gothra_name like '$data%' ", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);


        $view = new ViewModel(array("Results" => $result));
        $view->setTerminal(true);
        return $view;
        exit();
    }
   
}