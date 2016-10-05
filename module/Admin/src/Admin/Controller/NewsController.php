<?php

namespace Admin\Controller;

use Admin\Form\NewscategoryFilter;
use Admin\Form\NewscategoryForm;
use Admin\Form\NewsFilter;
use Admin\Form\NewsForm;
use Admin\Model\Entity\Newscategories;
use Admin\Model\Entity\Newses;
use Admin\Service\AdminServiceInterface;
use Admin\Service\NewsServiceInterface;
use Common\Service\CommonServiceInterface;
use Zend\View\Model\ViewModel;

class NewsController extends AppController {

    protected $data = '';
    protected $commonService;
    protected $adminService;
    protected $newsService;

    public function __construct(CommonServiceInterface $commonService, AdminServiceInterface $adminService, NewsServiceInterface $newsService) {

        $this->commonService = $commonService;
        $this->adminService = $adminService;
        $this->newsService = $newsService;
    }

    public function indexnewsAction() {
//        echo  "hello";exit;
//        $newses = $this->getNewsTable()->fetchAll();
        $newses = $this->newsService->getNewsList();


        return new ViewModel(array(
            'newses' => $newses));
    }

    public function addnewsAction() {
       
       
        $categoryNameList = $this->newsService->getAllNewscategory();

        NewsForm::$category_nameList = $categoryNameList;
       

        $form = new NewsForm();
//        echo   "hello";exit;
        $form->get('submit')->setAttribute('value', 'Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            
//            echo  "<pre>";
//            print_r($this->getRequest()->getFiles()->toArray());exit;

            $mergedata = array_merge(
                    $this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray()
            );
            
//            echo  "<pre>";
//            print_r($mergedata);exit;

            //$newsEntity = new Newses();

            //$form->setInputFilter(new NewsFilter());
            $form->setData($mergedata);


            if ($form->isValid()) {

                //$newsEntity->exchangeArray($form->getData());

                //$this->getNewsTable()->saveNews($newsEntity);
                $res= $this->newsService->saveNews($form->getData());
                

//                return $this->redirect()->toRoute('admin', array(
//                            'action' => 'indexnews',
//                            'controller' => 'news'
//                ));
                //return new JsonModel(array("response" => $res));
                return $this->redirect()->toRoute('admin/news', array('action' => 'indexnews'));
            }
        }

        return new ViewModel(array('form' => $form));
        
    }

    public function editnewsAction() {
//        echo  "hello";exit;
//        $categoryNameList = $this->getNewscategoryTable()->customFields(array('id', 'category_name'));
        $categoryNameList = $this->newsService->getAllNewscategory();

        NewsForm::$category_nameList = $categoryNameList;

        $form = new NewsForm();
        if ($this->params()->fromRoute('id') > 0) {
            $id = $this->params()->fromRoute('id');
//            $news = $this->getNewsTable()->getNews($id);
            $news = $this->newsService->getNews($id);
            // print_r($state);die;
            $form->bind($news);
            $form->get('submit')->setAttribute('value', 'Edit');
            // $this->editAction($form)
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
//            echo  "hello";exit;

            $mergedata = array_merge(
                    $this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray()
            );

//            $newsEntity = new Newses();

//            $form->setInputFilter(new NewsFilter());
            $form->setData($mergedata);


            if ($form->isValid()) {
//                echo  "hello";exit;

                // $newsEntity = $form->getData();
//                $newsEntity->exchangeArray($form->getData());
                // print_r($newsEntity);die;
//                $this->getNewsTable()->SaveNews($newsEntity);
                $res= $this->newsService->saveNews($form->getData());

//                return $this->redirect()->toRoute('admin', array(
//                            'action' => 'indexnews',
//                            'controller' => 'news'
//                ));
                return $this->redirect()->toRoute('admin/news', array('action' => 'indexnews'));
            }
        }

        return new ViewModel(array('form' => $form, 'id' => $id));
    }

    public function deletenewsAction() {
//        echo  "hello";exit;

        $id = $this->params()->fromRoute('id');
        // print_r($id);
//        $state = $this->getNewsTable()->deleteNews($id);
        $state= $this->adminService->delete('tbl_news', $id);
//        return $this->redirect()->toRoute('admin', array(
//                    'action' => 'indexnews',
//                    'controller' => 'news'
//        ));
        return $this->redirect()->toRoute('admin/news', array('action' => 'indexnews'));
    }

    public function viewnewsAction() {
        $id = $this->params()->fromRoute('id');

//        $Info = $this->getNewsTable()->getNewsjoin($id);
        $Info = $this->newsService->viewByNewsId('tbl_news', $id);
        
//        echo  "<pre>";
//                print_r($Info);exit;


        return new ViewModel(array('Info' => $Info));
    }

    public function indexnewscategoryAction() {
//        $newscategories = $this->getNewscategoryTable()->fetchAll();
        $newscategories = $this->newsService->getNewscategoryList();

        // print_r($newsecategories);die;
        return new ViewModel(array(
            'newscategories' => $newscategories, 'action'=>'indexnewscategory'));
    }

    public function addnewscategoryAction() {
//        echo "hello";exit;

        $form = new NewscategoryForm();
        $form->get('submit')->setAttribute('value', 'Add');

        $request = $this->getRequest();
        if ($request->isPost()) {

//            $newscategoryEntity = new Newscategories();

//            $form->setInputFilter(new NewscategoryFilter());
            $form->setData($request->getPost());


            if ($form->isValid()) {

//                $newscategoryEntity->exchangeArray($form->getData());
                // print_r($religionEntity);die;
                //$this->getNewscategoryTable()->SaveNewscategory($newscategoryEntity);
                $res= $this->newsService->SaveNewscategory($form->getData());
                

//                return $this->redirect()->toRoute('admin', array(
//                            'action' => 'indexnewscategory',
//                            'controller' => 'news'
//                ));
                return $this->redirect()->toRoute('admin/news', array('action' => 'indexnewscategory'));
            }
        }

        return new ViewModel(array('form' => $form));
    }

    public function editnewscategoryAction() {


        $form = new NewscategoryForm();
        if ($this->params()->fromRoute('id') > 0) {
            $id = $this->params()->fromRoute('id');
            // echo   $id;die;
//            $newscategory = $this->getNewscategoryTable()->getNewscategory($id);
            $newscategory= $this->newsService->getNewscategory($id);
            // print_r($religion);die;
            $form->bind($newscategory);
            $form->get('submit')->setAttribute('value', 'Edit');
            // $this->editAction($form)
        }

        $request = $this->getRequest();
        if ($request->isPost()) {

//            $newscategoryEntity = new Newscategories();

//            $form->setInputFilter(new NewscategoryFilter());
            $form->setData($request->getPost());


            if ($form->isValid()) {

//                $newscategoryEntity = $form->getData();
                // print_r($newscategoryEntity);die;
//                $this->getNewscategoryTable()->SaveNewscategory($newscategoryEntity);
                $res= $this->newsService->SaveNewscategory($form->getData());

//                return $this->redirect()->toRoute('admin', array(
//                            'action' => 'indexnewscategory',
//                            'controller' => 'news'
//                ));
                return $this->redirect()->toRoute('admin/news', array('action' => 'indexnewscategory'));
            }
        }

        return new ViewModel(array('form' => $form, 'id' => $id));
    }

    public function deletenewscategoryAction() {

        $id = $this->params()->fromRoute('id');
//        $state = $this->getNewscategoryTable()->deleteNewscategory($id);
        $state= $this->adminService->delete('tbl_newscategory', $id);
//        return $this->redirect()->toRoute('admin', array(
//                    'action' => 'indexnewscategory',
//                    'controller' => 'news'
//        ));
        return $this->redirect()->toRoute('admin/news', array('action' => 'indexnewscategory'));
    }

    public function viewnewscategoryAction() {
        $id = $this->params()->fromRoute('id');

//        $Info = $this->getNewscategoryTable()->getNewscategory($id);
        $Info = $this->newsService->viewByNewscategoryId('tbl_newscategory', $id);

        return new ViewModel(array('Info' => $Info));
    }
    
    public function ajaxradiosearchAction() {
        $status = $_POST['is_active'];
//        echo  "<pre>";
//        print_r($status);exit;
        //$this->data = array("IsActive=$status");
        $this->data = $status;

        //$religions = $this->getReligionTable()->fetchAll($this->data);
        $newscategories = $this->newsService->getNewscategoryRadioList($_POST['is_active']);
//        echo  "<pre>";
//        print_r($newscategories);exit;
        // return new ViewModel(array('countries' => $countries));

        $view = new ViewModel(array('newscategories' => $newscategories));
        $view->setTemplate('admin/news/newscategoryList');
        $view->setTerminal(true);
        return $view;
//        return new ViewModel(array(
//            'newscategories' => $newscategories));
    }
    
    public function newscategorysearchAction() {
        //$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $data = $_POST['value'];
//        echo  "<pre>";
//        print_r($data);die;

        //$result = $adapter->query("select * from tbl_religion where religion_name like '$data%' ", Adapter::QUERY_MODE_EXECUTE);
        $result = $this->newsService->newscategorySearch($data);


        $view = new ViewModel(array("Results" => $result));
        $view->setTerminal(true);
        return $view;
        exit();
    }
    
    public function performsearchAction() {
        //$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        //$field1 = empty($_POST['religion_name']) ? "" : "religion_name like '" . $_POST['religion_name'] . "%'";
        
        //$sql = "select * from tbl_religion where " . $field1 . "";
       // $sql = rtrim($sql, "&&");
        //$results = $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        $results = $this->newsService->performSearchNewscategory($_POST['category_name']);

        $view = new ViewModel(array("results" => $results));
        $view->setTerminal(true);
        return $view;


        exit();
    }

}
