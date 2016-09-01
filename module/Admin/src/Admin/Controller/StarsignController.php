<?php

namespace Admin\Controller;

use Admin\Form\StarsignFilter;
use Admin\Form\StarsignForm;
use Admin\Model\Entity\Starsigns;
use Admin\Service\AdminServiceInterface;
use Common\Service\CommonServiceInterface;
use Zend\Db\Adapter\Adapter;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class StarsignController extends AppController
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
         $starsigns = $this->getStarsignTable()->fetchAll($this->data);

         // print_r($gothras);die;
		 
		  $form = new StarsignForm();
         $form->get('submit')->setAttribute('value', 'Add');

        return new ViewModel(array(
            'starsigns' => $starsigns,'form'=> $form, 'action'=>'index'));

//        return new ViewModel(array(
//            'starsigns' => $starsigns));
    }

    public function AddAction()
    {
        $form = new StarsignForm();
        $form->get('submit')->setAttribute('value', 'Add');

        $request = $this->getRequest();
        if($request->isPost()){

            $starsignEntity = new Starsigns();

               $form->setInputFilter(new StarsignFilter());
               $form->setData($request->getPost());


               if($form->isValid()){

                $starsignEntity->exchangeArray($form->getData());
                // print_r($religionEntity);die;
                $res = $this->getStarsignTable()->SaveStarsign($starsignEntity);

//                     return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'starsign'
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
        

        $form = new StarsignForm();
        if($this->params()->fromRoute('id')>0){
            $id = $this->params()->fromRoute('id');
            // echo   $id;die;
            $starsign = $this->getStarsignTable()->getStarsign($id);
            // print_r($religion);die;
            $form->bind($starsign);
            $form->get('submit')->setAttribute('value', 'Edit');
            // $this->editAction($form)
        }

        $request = $this->getRequest();
        if (!isset($_POST['chkedit'])) {
        if($request->isPost()){

            $starsignEntity = new Starsigns();

               $form->setInputFilter(new StarsignFilter());
               $form->setData($request->getPost());


               if($form->isValid()){

                $starsignEntity = $form->getData();
                // print_r($cityEntity);die;
                $res = $this->getStarsignTable()->SaveStarsign($starsignEntity);

//                     return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'starsign'
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
            $starsign = $this->getStarsignTable()->deleteStarsign($id);
//            return $this->redirect()->toRoute('admin', array(
//                            'action' => 'index',
//                            'controller' => 'starsign'
//                ));
            return $this->redirect()->toRoute('admin/starsign', array('action' => 'index'));
    }
    
    public function viewAction()
    {
        $id = $this->params()->fromRoute('id');

        $Info = $this->getStarsignTable()->getStarsign($id);

        return new ViewModel(array('Info'=> $Info));
    }
    
     public function viewByIdAction(){
        
        $id = $this->params()->fromRoute('id');

        //$Info = $this->getCountryTable()->getCountry($id);
        $info = $this->getStarsignTable()->getStarsign($id);

        // echo"<pre>"; print_r($Info);die;
        $view=new ViewModel(array('info'=>$info));
        $view->setTerminal(true);
        return $view;
        
    }
    
    public function changestatusAction() {

        $data = (object) $_POST;
        $return = $this->getStarsignTable()->updatestatus($data);
        // print_r($return);
        return new JsonModel($return);
        exit();
    }
    
    public function delmultipleAction() {
        $ids = $_POST['chkdata'];
        $result = $this->getStarsignTable()->delmultiple($ids);

        echo $result;
        exit();
    }
    
    public function statuschangeallAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $sql = "update tbl_star_sign set IsActive=" . $_POST['val'] . " where id IN (" . $_POST['ids'] . ")";
        $results = $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        if ($results)
            echo "updated all";
        else
            echo "couldn't update";
        exit();
    }
    
    public function ajaxradiosearchAction() {
        $status = $_POST['IsActive'];
        $this->data = array("IsActive=$status");

        $starsigns = $this->getStarsignTable()->fetchAll($this->data);
        // return new ViewModel(array('countries' => $countries));

        $view = new ViewModel(array('starsigns' => $starsigns));
        $view->setTemplate('admin/starsign/starsignList');
        $view->setTerminal(true);
        return $view;
    }
    
    public function performsearchAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $field1 = empty($_POST['star_sign_name']) ? "" : "star_sign_name like '" . $_POST['star_sign_name'] . "%'";
        
        $sql = "select * from tbl_star_sign where " . $field1 . "";
       // $sql = rtrim($sql, "&&");
        $results = $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        $view = new ViewModel(array("results" => $results));
        $view->setTerminal(true);
        return $view;


        exit();
    }
    
    public function starsignsearchAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $data = $_POST['value'];
//        echo  "<pre>";
//        print_r($data);die;

        $result = $adapter->query("select * from tbl_star_sign where star_sign_name like '$data%' ", Adapter::QUERY_MODE_EXECUTE);


        $view = new ViewModel(array("Results" => $result));
        $view->setTerminal(true);
        return $view;
        exit();
    }
   
}