<?php

namespace Admin\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Admin\Model\Entity\Zodiacsigns;
use Admin\Form\ZodiacsignForm;
use Admin\Form\ZodiacsignFilter;

class ZodiacsignController extends AppController
{
    protected $data = array();
    
    public function indexAction()
    {   
         $zodiacsigns = $this->getZodiacsignTable()->fetchAll($this->data);
            //echo   "<pre>";
          //print_r($zodiacsigns);die;
         // print_r($cities);die;
		 
		   
		$form = new ZodiacsignForm();
         $form->get('submit')->setAttribute('value', 'Add');

        return new ViewModel(array(
            'zodiacsigns' => $zodiacsigns,'form'=> $form, 'action'=>'index'));
			
    }

    public function AddAction()
    {
        $form = new ZodiacsignForm();
        $form->get('submit')->setAttribute('value', 'Add');

        $request = $this->getRequest();
        if($request->isPost()){

            $zodiacsignEntity = new Zodiacsigns();

               $form->setInputFilter(new ZodiacsignFilter());
               $form->setData($request->getPost());


               if($form->isValid()){

                $zodiacsignEntity->exchangeArray($form->getData());
                // print_r($zodiacsignEntity);die;
                $res = $this->getZodiacsignTable()->SaveZodiacsign($zodiacsignEntity);

//                     return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'zodiacsign'
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
        $form = new ZodiacsignForm();
        if($this->params()->fromRoute('id')>0){
            $id = $this->params()->fromRoute('id');
            // echo   $id;die;
            $zodiacsign = $this->getZodiacsignTable()->getZodiacsign($id);
            // print_r($zodiacsign);die;
            $form->bind($zodiacsign);
            $form->get('submit')->setAttribute('value', 'Edit');
            // $this->editAction($form)
        }

        $request = $this->getRequest();
        if (!isset($_POST['chkedit'])) {
        if($request->isPost()){

            $zodiacsignEntity = new Zodiacsigns();

               $form->setInputFilter(new ZodiacsignFilter());
               $form->setData($request->getPost());


               if($form->isValid()){

                $zodiacsignEntity = $form->getData();
                // print_r($cityEntity);die;
                $res = $this->getZodiacsignTable()->SaveZodiacsign($zodiacsignEntity);

//                     return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'zodiacsign'
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
            $zodiacsign = $this->getZodiacsignTable()->deleteZodiacsign($id);
//            return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'zodiacsign'
//                ));
            return $this->redirect()->toRoute('admin/zodiacsign', array('action' => 'index'));
    }
    
    public function viewAction()
    {
        $id = $this->params()->fromRoute('id');

        $Info = $this->getZodiacsignTable()->getZodiacsign($id);

        return new ViewModel(array('Info'=> $Info));
    }
    
    public function viewByIdAction(){
        
        $id = $this->params()->fromRoute('id');

        //$Info = $this->getCountryTable()->getCountry($id);
        $info = $this->getZodiacsignTable()->getZodiacsign($id);

        // echo"<pre>"; print_r($Info);die;
        $view=new ViewModel(array('info'=>$info));
        $view->setTerminal(true);
        return $view;
        
    }
    
    public function changestatusAction() {

        $data = (object) $_POST;
        $return = $this->getZodiacsignTable()->updatestatus($data);
        // print_r($return);
        return new JsonModel($return);
        exit();
    }
    
    public function delmultipleAction() {
        $ids = $_POST['chkdata'];
        $result = $this->getZodiacsignTable()->delmultiple($ids);

        echo $result;
        exit();
    }
    
    public function statuschangeallAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $sql = "update tbl_zodiac_sign_raasi set IsActive=" . $_POST['val'] . " where id IN (" . $_POST['ids'] . ")";
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

        $zodiacsigns = $this->getZodiacsignTable()->fetchAll($this->data);
        // return new ViewModel(array('countries' => $countries));

        $view = new ViewModel(array('zodiacsigns' => $zodiacsigns));
        $view->setTemplate('admin/zodiacsign/zodiacsignList');
        $view->setTerminal(true);
        return $view;
    }
    
    public function performsearchAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $field1 = empty($_POST['zodiac_sign_name']) ? "" : "zodiac_sign_name like '" . $_POST['zodiac_sign_name'] . "%'";
        
        $sql = "select * from tbl_zodiac_sign_raasi where " . $field1 . "";
       // $sql = rtrim($sql, "&&");
        $results = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        $view = new ViewModel(array("results" => $results));
        $view->setTerminal(true);
        return $view;


        exit();
    }
    
    public function zodiacsignsearchAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $data = $_POST['value'];
//        echo  "<pre>";
//        print_r($data);die;

        $result = $adapter->query("select * from tbl_zodiac_sign_raasi where zodiac_sign_name like '$data%' ", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);


        $view = new ViewModel(array("Results" => $result));
        $view->setTerminal(true);
        return $view;
        exit();
    }
   
}