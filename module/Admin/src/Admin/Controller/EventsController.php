<?php

namespace Admin\Controller;

use Admin\Form\EventsFilter;
use Admin\Form\EventsForm;
use Admin\Model\Entity\Events;
use Admin\Service\AdminServiceInterface;
use Common\Service\CommonServiceInterface;
use Admin\Service\EventsServiceInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class EventsController extends AppController
{
    protected $commonService;
    protected $adminService;
    protected $eventsService;

    public function __construct(CommonServiceInterface $commonService, AdminServiceInterface $adminService, EventsServiceInterface $eventsService) {
        $this->commonService = $commonService;
        $this->adminService = $adminService;
        $this->eventsService = $eventsService;
    }
    
    public function eventsindexAction()
    {   
//        echo  "hello";
//        exit;
        
//        $events = $this->eventsService->test();
//        echo  $events;exit;
    
    
//        $countryNameList = $this->getCountryTable()->customFields(array('id','country_name'));
        $countryNameList = $this->adminService->customFields();
//        $cityNameList = $this->getCityTable()->customFields(array('id','city_name'));
        $cityNameList = $this->eventsService->customFieldsCity();
//        $stateNameList = $this->getStateTable()->customFields(array('id','state_name'));
        $stateNameList = $this->adminService->customFieldsState();
        

        EventsForm::$country_nameList = $countryNameList;
        EventsForm::$city_nameList = $cityNameList;
        EventsForm::$state_nameList = $stateNameList;

//         print_r($cityNameList); die;

        $form = new EventsForm();
        $form->get('submit')->setAttribute('value', 'Add This Event');

//         $events = $this->getEventsTable()->fetchAll();
         $events = $this->eventsService->getEventsList();
//         \Zend\Debug\Debug::dump($events);exit;

// print_r("dfghdf");die; 

        return new ViewModel(array(
            'events' => $events,'form'=> $form));
    }


    public function eventsaddAction()
    {
//        $events = $this->eventsService->test();
//        echo  $events;exit;
        
//        $countryNameList = $this->getCountryTable()->customFields(array('id','country_name'));
        $countryNameList = $this->adminService->customFields();
//        $cityNameList = $this->getCityTable()->customFields(array('id','city_name'));
        $cityNameList = $this->eventsService->customFieldsCity();
//        $stateNameList = $this->getStateTable()->customFields(array('id','state_name'));
        $stateNameList = $this->adminService->customFieldsState();
        
        
        

        EventsForm::$country_nameList = $countryNameList;
        EventsForm::$city_nameList = $cityNameList;
        EventsForm::$state_nameList = $stateNameList;
        
//        echo   "<pre>";
//         print_r($cityNameList); die;
//                echo  "hello";exit;
        $form = new EventsForm();
//        echo  "hello";exit;
        $form->get('submit')->setAttribute('value', 'Add This Event');

        $request = $this->getRequest();
        if($request->isPost()){

           $mergedata = array_merge(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
           // echo "fghf";
           // print_r($mergedata);die;
            //$eventsEntity = new Events();

              // $form->setInputFilter(new EventsFilter());
               $form->setData($mergedata);


               if($form->isValid()){

                //$eventsEntity->exchangeArray($form->getData());
                
//                $this->getEventsTable()->SaveEvents($eventsEntity);
                $res= $this->eventsService->saveEvents($form->getData());

//                     return $this->redirect()->toRoute('admin', array(
//                            'action' => 'eventsindex',
//                            'controller' => 'events'
//                ));
                     return $this->redirect()->toRoute('admin/events', array('action' => 'eventsindex'));
               }
        }

        return new ViewModel(array('form'=> $form));
        
    }

    public function eventseditAction()
    {       //echo  "hello amir";exit;
         
//        $countryNameList = $this->getCountryTable()->customFields(array('id','country_name'));
        $countryNameList = $this->adminService->customFields();
//        $cityNameList = $this->getCityTable()->customFields(array('id','city_name'));
        $cityNameList = $this->eventsService->customFieldsCity();
//        $stateNameList = $this->getStateTable()->customFields(array('id','state_name'));
        $stateNameList = $this->adminService->customFieldsState();

        EventsForm::$country_nameList = $countryNameList;
        EventsForm::$city_nameList = $cityNameList;
        EventsForm::$state_nameList = $stateNameList;
//        echo  "hello amir2";exit;
        $form = new EventsForm();
         if($this->params()->fromRoute('id') > 0){
            $id = $this->params()->fromRoute('id');
//            $events = $this->getEventsTable()->getEvents($_POST['id']);
            $events = $this->eventsService->getEvents($id);
             
            $form->bind($events);
         $form->get('submit')->setAttribute('value', 'Edit');
         //print_r($events);die;
        }
//        if($this->params()->fromRoute('id')>0){
//            $id = $this->params()->fromRoute('id');
//            $events = $this->getEventsTable()->getEvents($_POST['id']);
//            // print_r($state);die;
//            $form->bind($events);
//            $form->get('submit')->setAttribute('value', 'Save');
//            // $this->editAction($form)
//        }

        $request = $this->getRequest();
        //if(!isset($_POST['chkedit'])){
        if($request->isPost()){
            // echo "dfsgdsfg";

           $mergedata = array_merge(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
            
            //$eventsEntity = new Events();

               //$form->setInputFilter(new EventsFilter());
               $form->setData($mergedata);


               if($form->isValid()){

               // $eventsEntity->exchangeArray($form->getData());
                // print_r($eventsEntity);die;
                
                //$this->getEventsTable()->SaveEvents($eventsEntity);
                $res= $this->eventsService->saveEvents($form->getData());

//                     return $this->redirect()->toRoute('admin', array(
//                            'action' => 'eventsindex',
//                            'controller' => 'events'
//                ));
                return $this->redirect()->toRoute('admin/events', array('action' => 'eventsindex'));
               }
            }
        //}

//        $view = new ViewModel(array('form'=> $form,'id'=>$id));
//        $view->setTerminal(true);
//        return $view;
        // echo $_POST['id'];
        // die;
        return new ViewModel(array('form' => $form, 'id' => $id));
    }

    public function eventsdeleteAction()
    {
         
            $id = $this->params()->fromRoute('id');
            // print_r($id);
//            $state = $this->getEventsTable()->deleteEvents($id);
            $state= $this->adminService->delete('tbl_upcoming_events', $id);
//            return $this->redirect()->toRoute('admin', array(
//                            'action' => 'eventsindex',
//                            'controller' => 'events'
//                ));
            return $this->redirect()->toRoute('admin/events', array('action' => 'eventsindex'));
    }
    

    
    public function eventsviewAction()
    {
//        $id = $_POST['id'];
        $id = $this->params()->fromRoute('id');

//        $Info = $this->getEventsTable()->getEventsjoin($id);
        $info = $this->eventsService->viewByEventsId('tbl_upcoming_events', $id);

//            echo   "<pre>";
//            print_r($info);exit;
//        \Zend\Debug\Debug::dump($info);exit;

        $view = new ViewModel(array('info'=> $info));
        $view->setTerminal(true);
        return $view;
    }

    public function changestatusAction()
    {   

//        $data = (object)$_POST;
        $request=$this->getRequest();
//        $return = $this->getEventsTable()->updatestatus($data);
        $result= $this->adminService->changeStatus('tbl_upcoming_events', $request->getPost('id'), $request->getPost());
        // print_r($return);
        //return new JsonModel($return);
        return new JsonModel($result);
        //exit();

    }


    // public function indexnewscategoryAction()
    // {   
    //      $newscategories = $this->getNewscategoryTable()->fetchAll();

    //      // print_r($newsecategories);die;
    //     return new ViewModel(array(
    //         'newscategories' => $newscategories));
    // }

    // public function addnewscategoryAction()
    // {
        

    //     $form = new NewscategoryForm();
    //     $form->get('submit')->setAttribute('value', 'Add');

    //     $request = $this->getRequest();
    //     if($request->isPost()){

    //         $newscategoryEntity = new Newscategories();

    //            $form->setInputFilter(new NewscategoryFilter());
    //            $form->setData($request->getPost());


    //            if($form->isValid()){

    //             $newscategoryEntity->exchangeArray($form->getData());
    //             // print_r($religionEntity);die;
    //             $this->getNewscategoryTable()->SaveNewscategory($newscategoryEntity);

    //                  return $this->redirect()->toRoute('admin', array(
    //                         'action' => 'indexnewscategory',
    //                         'controller' => 'news'
    //             ));
    //            }
    //     }

    //     return new ViewModel(array('form'=> $form));
        
    // }

   
    // public function viewnewscategoryAction()
    // {
    //     $id = $this->params()->fromRoute('id');

    //     $Info = $this->getNewscategoryTable()->getNewscategory($id);

    //     return new ViewModel(array('Info'=> $Info));
    // }
   
}