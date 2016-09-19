<?php

namespace Application\Controller;

use Application\Form\AboutForm;
use Application\Form\EducationAndCareerForm;
use Application\Form\EducationForm;
use Application\Form\FamilyInfoForm;
use Application\Form\Filter\EducationAndCareerFormFilter;
use Application\Form\Filter\Family;
use Application\Form\MetrimoniForm;
use Application\Form\PersonolDetailForm;
use Application\Form\PostForm;
use Application\Form\ProfessionForm;
use Application\Model\Entity\Career;
use Application\Model\Entity\Matrimoni;
use Application\Service\ProfileServiceInterface;
use Application\Service\UserServiceInterface;
use Common\Service\CommonServiceInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Metadata\Metadata;
use Zend\Debug\Debug;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\TemplateMapResolver;

class ProfileController extends AppController {

    protected $data = array();
    protected $accountService;
    protected $userService;
    protected $commonService;

    /**
     * Attache les évènements
     * @see \Zend\Mvc\Controller\AbstractController::attachDefaultListeners()
     */
    protected function attachDefaultListeners() {
        parent::attachDefaultListeners();

        $events = $this->getEventManager();
        $events->attach('dispatch', array($this, 'preDispatch'), 100);
        $events->attach('dispatch', array($this, 'postDispatch'), -100);
    }

    /**
     * Avant l'action
     * @param MvcEvent $e
     */
    public function preDispatch(MvcEvent $e) {
        $this->checkUserLogin();

//         $actioList=array('personal-profile', 'education-and-career');
//         if(in_array($this->params('action'), $actioList)){
//           $this->checkUserLogin();
//         }
    }

    /**
     * Après l'action
     * @param MvcEvent $e
     */
    public function postDispatch(MvcEvent $e) {
        
    }

    public function __construct(ProfileServiceInterface $accountService, CommonServiceInterface $commonService, UserServiceInterface $userService) {
        $this->accountService = $accountService;
        $this->userService = $userService;
        $this->commonService = $commonService;
    }

    public function indexAction() {
        echo 'satya';
    }

    public function profileAction() {
        
    }

    public function personalProfileAction() {

        //Debug::dump($this->checkLogin());
        //$this->checkLogin();
        //exit;
        $userSession = $this->getUser()->session();
        $user_id = $userSession->offsetGet('id');
        $ref_no = $userSession->offsetGet('ref_no');
        $MemberbasicForm = new PersonolDetailForm($this->commonService);
        //$personalDetails = new PersonalDetails();
        $info = $this->userService->getUserPersonalDetailById($user_id);
        $MemberbasicForm->bind($info);
        $request = $this->getRequest();
        if ($request->isPost()) {
            //$page = new Memberbasic();
            //$MemberbasicForm->setInputFilter($page->getInputFilter());
            $MemberbasicForm->setData($request->getPost());
            //$data = (array) $request->getPost();
            if ($MemberbasicForm->isValid()) {
                //$personalDetailsExchange=new \Application\Model\Entity\PersonalprofileExchange();
                //$ddad=$personalDetailsExchange->exchangeArrayTable($hydrator->extract($MemberbasicForm->getData()));
                //Debug::dump($request->getPost()->submit);
                //exit;
                $this->userService->saveUserPersonalDetails($info);
                //exit;
//                $page->exchangeArray($data);
//                unset($page->inputFilter);
//                $page->dob = date('Y-m-d', strtotime($page->dob));
//                $id = $this->getUserInfoTable()->saveUserData($page);
                // if($id>0){
                //   }
//                if ($button == "Save") {
//                    return $this->redirect()->toRoute('application/default', array(
//                                'action' => 'memberbasic',
//                                'controller' => 'account'
//                    ));
//                }
                if ($request->getPost()->submit == "Save & Next") {
                    $this->redirect()->toRoute("application/default", array(
                        "action" => "editeducation",
                        "controller" => "account",
                    ));
                }
            }
        }

        $percentage = $this->userService->ProfileBar($user_id);
        $pro_per = array($percentage, $this->profileBarTemplate($percentage));
        //Debug::dump($this->userService->userSummaryById($user_id));
        return new ViewModel(array("form" => $MemberbasicForm,
            'userSummary' => $this->userService->userSummaryById($user_id),
            "percent" => $pro_per));
    }

    public function LogoutAction() {
        $session = new Container('user');
        $session->getManager()->getStorage()->clear('user');
        return $this->redirect()->toRoute("application/default", array('controller' => 'index', 'action' => 'index'));
    }

    public function personalDetailAction() {

        $userSession = $this->getUser()->session();
        $user_id = $userSession->offsetGet('id');
        $ref_no = $userSession->offsetGet('ref_no');
        $MemberbasicForm = new PersonolDetailForm($this->commonService);
        //$personalDetails = new PersonalDetails();
        $info = $this->userService->getUserPersonalDetailById($user_id);
        $MemberbasicForm->bind($info);
        $request = $this->getRequest();
        if ($request->isPost()) {
            //$page = new Memberbasic();
            //$MemberbasicForm->setInputFilter($page->getInputFilter());
            $MemberbasicForm->setData($request->getPost());
            //$data = (array) $request->getPost();
            if ($MemberbasicForm->isValid()) {
                //$personalDetailsExchange=new \Application\Model\Entity\PersonalprofileExchange();
                //$ddad=$personalDetailsExchange->exchangeArrayTable($hydrator->extract($MemberbasicForm->getData()));
                //Debug::dump($request->getPost()->submit);
                //exit;
                $this->userService->saveUserPersonalDetails($info);
                //exit;
//                $page->exchangeArray($data);
//                unset($page->inputFilter);
//                $page->dob = date('Y-m-d', strtotime($page->dob));
//                $id = $this->getUserInfoTable()->saveUserData($page);
                // if($id>0){
                //   }
//                if ($button == "Save") {
//                    return $this->redirect()->toRoute('application/default', array(
//                                'action' => 'memberbasic',
//                                'controller' => 'account'
//                    ));
//                }
                if ($request->getPost()->submit == "Save & Next") {
                    $this->redirect()->toRoute("application/default", array(
                        "action" => "editeducation",
                        "controller" => "account",
                    ));
                }
            }
        }

        $percentage = $this->userService->ProfileBar($user_id);
        $pro_per = array($percentage, $this->profileBarTemplate($percentage));

        return new ViewModel(array("form" => $MemberbasicForm,
            'userSummary' => $this->userService->userSummaryById($user_id),
            "percent" => $pro_per));
    }

    public function educationAndCareerAction() {

        $userSession = $this->getUser()->session();
        $user_id = $userSession->offsetGet('id');
        $ref_no = $userSession->offsetGet('ref_no');
        $form = new EducationAndCareerForm($this->commonService);
        $data = $this->userService->getUserEducationAndCareerDetailById($user_id);
        $form->bind($data);
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setInputFilter(new EducationAndCareerFormFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {

                $this->userService->saveUserEducationAndCareerDetail($data);
                if ($request->getPost()->submit == "Save & Next") {
                    $this->redirect()->toRoute("application/default", array(
                        "action" => "family",
                        "controller" => "account",
                    ));
                }
            }
        }
        //$data_gallery = $adapter->query("select * from tbl_user_gallery where user_id='$user_id' AND ref_no='$ref_no' ORDER BY id DESC", Adapter::QUERY_MODE_EXECUTE);
        $percentage = $this->userService->ProfileBar($user_id);
        $pro_per = array($percentage, $this->profileBarTemplate($percentage));
        //Debug::dump($pro_per);

        return new ViewModel(array("form" => $form,
            'userSummary' => $this->userService->userSummaryById($user_id),
            "percent" => $pro_per));
    }

    public function educationAction() {

        $userSession = $this->getUser()->session();
        $user_id = $userSession->offsetGet('id');
        $ref_no = $userSession->offsetGet('ref_no');
        $EducationForm = new EducationForm($this->commonService);
        //$educationDetails = new Education();
        $info = $this->userService->educationDetailById($user_id);
        $EducationForm->bind($info);
        $request = $this->getRequest();




//        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
//        $session = new Container('user');
//        $user_id = $session->offsetGet('id');
//        $ref_no = $session->offsetGet('ref_no');
//        $education_level = $this->getEducationLevelTable()->selectList(array('id', 'education_level'));
//        EducationForm::$educationList = $education_level;
//        $education_field = $this->getEducationFieldTable()->selectList(array('id', 'education_field'));
//        EducationForm::$education_fieldList = $education_field;
//        EducationForm::$Employment_status = $this->EmploymentStatus();
//        $professionList = $this->getProfessionTable()->selectList(array('id', 'profession'));
//        EducationForm::$professionTypeList = $professionList;
//        $udata = $this->getUserInfoTable()->GetUserEducation($session->offsetGet('id'));
//        $EducationForm = new EducationForm();
//        $info = $this->userService->educationDetailById($user_id);
//        $educationDetails = new Education();
//        $EducationForm->bind($educationDetails->exchangeArray($info));
//        $request = $this->getRequest();
        if ($request->isPost()) {
//            $page = new Education();
//            $EducationForm->setInputFilter($page->getInputFilter());

            $EducationForm->setData($request->getPost());
            //$data = (array) $request->getPost();
            if ($EducationForm->isValid()) {

                //Debug::dump($info);
                //exit;
                $this->userService->saveUserEducationDetails($info);
//                unset($page->inputFilter);
//                $id = $this->getUserInfoTable()->saveUserEducation($page);
                // if($id>0){
                // return $this->redirect()->toRoute('application/default', array(
                // 			'action' => 'editeducation',
                // 			'controller' => 'account'
                // ));
                //   }


                if ($request->getPost()->submit == "Save & Next") {
                    $this->redirect()->toRoute("application/default", array(
                        "action" => "editcareer",
                        "controller" => "account",
                    ));
                }
            }
        }
        //$data_gallery = $adapter->query("select * from tbl_user_gallery where user_id='$user_id' AND ref_no='$ref_no' ORDER BY id DESC", Adapter::QUERY_MODE_EXECUTE);
        //$pro_per = $this->ProfileBar();

        return new ViewModel(array("form" => $EducationForm));
    }

    public function professionAction() {

        $userSession = $this->getUser()->session();
        $user_id = $userSession->offsetGet('id');
        $ref_no = $userSession->offsetGet('ref_no');
        $professionForm = new ProfessionForm($this->commonService);
        //$educationDetails = new Education();
        $info = $this->userService->getUserProfessionById($user_id, array('id', 'ref_no'));
        //Debug::dump($info);
        //exit;
        $professionForm->bind($info);
        $request = $this->getRequest();

//        $CareerForm = new CareerForm();
//        $CareerForm->bind($udata);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $page = new Career();
            $CareerForm->setInputFilter($page->getInputFilter());
            $CareerForm->setData($request->getPost());
            $data = (array) $request->getPost();
            //if($CareerForm->isValid()) {					/
            $page->exchangeArray($data);
            unset($page->inputFilter);
            $id = $this->getUserInfoTable()->saveUserCareer($page);
            // if($id>0){
            // return $this->redirect()->toRoute('application/default', array(
            // 			'action' => 'editcareer',
            // 			'controller' => 'account'
            // ));
            //   }


            if ($request->getPost()->submit == "Save & Next") {
                $this->redirect()->toRoute("application/default", array(
                    "action" => "family",
                    "controller" => "account",
                ));
            }
            //}          			
        }
        //$data_gallery = $adapter->query("select * from tbl_user_gallery where user_id='$user_id' AND ref_no='$ref_no' ORDER BY id DESC", Adapter::QUERY_MODE_EXECUTE);
        //$pro_per = $this->ProfileBar();

        return new ViewModel(array("form" => $professionForm));
    }

    public function familyDetailAction() {
        $userSession = $this->getUser()->session();
        $user_id = $userSession->offsetGet('id');
        $ref_no = $userSession->offsetGet('ref_no');

        FamilyInfoForm::$Employment_status = $this->LiveStatus();
        FamilyInfoForm::$Family_Values = $this->FamilyValuesStatus();
        FamilyInfoForm::$Name_Title = $this->GetNameTitle();
        //$familyInfo = $this->getFamilyInfoTable()->getFamilyInfo($session->offsetGet('id'));
        // print_r($udata->mother_photo);die;
        $FamilyInfoForm = new FamilyInfoForm();
        $familyInfo = $this->userService->getFamilyInfoById($user_id);
        $userInfo = $this->userService->getUserInfoById($user_id, array('marital_status'));
        //Debug::dump($familyInfo->sisterData);
        //\Zend\Debug\Debug::dump($this->userService->getFamilyInfoById($user_id));
        //exit;
        //$FamilyInfoForm->get('user_id')->setValue($session->offsetGet('id'));
//        foreach ($familyInfo->brotherData as $results) {
//            \Zend\Debug\Debug::dump($results);
//        }
        //\Zend\Debug\Debug::dump($familyInfo->brotherData);
        //exit;
        //\Zend\Debug\Debug::dump($familyInfo->familyInfoObject);
        $FamilyInfoForm->bind($familyInfo->familyInfoObject);
        $request = $this->getRequest();

        if ($request->isPost()) {
            //$page = new Family();
            $FamilyInfoForm->setInputFilter(new Family());
            $FamilyInfoForm->setData($request->getPost());
            if ($FamilyInfoForm->isValid()) {
                $this->userService->saveFamilyInfo($user_id, $request->getPost());
                //Debug::dump();
                //exit;
            } elseif (!$FamilyInfoForm->isValid()) {

                $errors = $FamilyInfoForm->getMessages();
                foreach ($errors as $key => $row) {
                    if (!empty($row) && $key != 'submit') {
                        foreach ($row as $keyer => $rower) {
                            //save error(s) per-element that
                            //needed by Javascript
                            $messages[$key][] = $rower;
                        }
                    }
                }
            }

            // Debug::dump($messages);
            // exit;
            // \Zend\Debug\Debug::dump($request->getPost());
        }


        $percentage = $this->userService->ProfileBar($user_id);
        $pro_per = array($percentage, $this->profileBarTemplate($percentage));
        //Debug::dump($pro_per);


        $broDataJson = \Zend\Json\Json::encode($familyInfo->brotherData);
        $sisDataJson = \Zend\Json\Json::encode($familyInfo->sisterData);
        $kidsDataJson = \Zend\Json\Json::encode($familyInfo->kidsData);


        return new ViewModel(array("form" => $FamilyInfoForm,
            'userInfo' => $userInfo,
            'familyInfoObject' => $familyInfo->familyInfoObject,
            'brotherData' => $familyInfo->brotherData,
            'sisterData' => $familyInfo->sisterData,
            'kidsData' => $familyInfo->kidsData,
            'broDataJson' => $broDataJson,
            'sisDataJson' => $sisDataJson,
            'kidsDataJson' => $kidsDataJson,
            'familyInfoArray' => $familyInfo->familyInfoArray,
            'GalleryInfo' => $familyInfo->GalleryInfo,
            'userSummary' => $this->userService->userSummaryById($user_id),
            "percent" => $pro_per));
    }

    public function matrimoniAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $session = new Container('user');
        $user_id = $session->offsetGet('id');
        $ref_no = $session->offsetGet('ref_no');
        $religion_name = $this->getReligionTable()->selectList(array('id', 'religion_name'));
        $gothra_name = $this->getGothraTable()->selectList(array('id', 'gothra_name'));
        MetrimoniForm::$gothra_nameList = $gothra_name;
        MetrimoniForm::$religion_nameList = $religion_name;
        MetrimoniForm::$blood_group = $this->BloodGroup();
        MetrimoniForm::$marital_status = $this->MeritalStatus();
        $MetrimoniForm = new MetrimoniForm();
        $udata = $this->getUserInfoTable()->GetUserMatrimoni($session->offsetGet('id'));
        $MetrimoniForm = new MetrimoniForm();
        $id = $udata->id;
        $MetrimoniForm->bind($udata);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $page = new Matrimoni();
            $MetrimoniForm->setInputFilter($page->getInputFilter());
            $MetrimoniForm->setData($request->getPost());
            $data = (array) $request->getPost();
            if ($MetrimoniForm->isValid()) {
                $page->exchangeArray($data);
                unset($page->inputFilter);
                $page->id = $id;
                $page->user_id = $session->offsetGet('id');
                $id = $this->getUserInfoTable()->saveUserMatrimoni($page);
                if ($id > 0) {
                    return $this->redirect()->toRoute('application/default', array(
                                'action' => 'editmatrimoni',
                                'controller' => 'account'
                    ));
                }
            }
        }
        $data_gallery = $adapter->query("select * from tbl_user_gallery where user_id='$user_id' AND ref_no='$ref_no' ORDER BY id DESC", Adapter::QUERY_MODE_EXECUTE);

        $pro_per = $this->ProfileBar();


        return new ViewModel(array("form" => $MetrimoniForm, "gallery_data" => $data_gallery));
    }

    public function aboutAction() {

        $userSession = $this->getUser()->session();
        $user_id = $userSession->offsetGet('id');
        $ref_no = $userSession->offsetGet('ref_no');


        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form = new AboutForm();
        $info = $this->userService->getUserAboutById($user_id);
        //Debug::dump($info);
        //exit;
        $form->bind($info);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {

                $this->userService->saveUserAbout($info);
                if ($request->getPost('about_meSave') == "Save & Next") {
                    $this->redirect()->toRoute("application/default", array(
                        "action" => "about",
                        "controller" => "account",
                    ));
                }
            }
            //$text = $request->getPost("about_Yourself");
            //Debug::dump(trim($text));
            //exit;
            //$adapter->query("UPDATE tbl_user_info set about_yourself_partner_family='$text' where user_id='$user_id' AND ref_no='$ref_no'", Adapter::QUERY_MODE_EXECUTE);
        }
        //$data = $adapter->query("select about_yourself_partner_family as about_me from tbl_user_info where user_id='$user_id' AND ref_no='$ref_no'", Adapter::QUERY_MODE_EXECUTE);
        //$data_gallery = $adapter->query("select * from tbl_user_gallery where user_id='$user_id' AND ref_no='$ref_no' ORDER BY id DESC", Adapter::QUERY_MODE_EXECUTE);
        //Debug::dump($request->getPost('about_meSave'));
        // exit;



        $percentage = $this->userService->ProfileBar($user_id);
        $pro_per = array($percentage, $this->profileBarTemplate($percentage));

        return new ViewModel(array("form" => $form,
            'userSummary' => $this->userService->userSummaryById($user_id),
            "percent" => $pro_per));
    }

    public function mygalleryAction() {
        //Debug::dump($this->options->getBasePath());
        //\Zend\Debug\Debug::dump(PUBLIC_PATH);
        $userSession = $this->getUser()->session();
        $user_id = $userSession->offsetGet('id');
        $ref_no = $userSession->offsetGet('ref_no');



        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $data = $adapter->query("select * from tbl_user_gallery where user_id='$user_id' AND ref_no='$ref_no' ORDER BY id DESC limit 6", Adapter::QUERY_MODE_EXECUTE)->toArray();
        // Debug::dump($data);
//        $metadata = new Metadata($adapter);
//        $table = $metadata->getTable("tbl_family_info");
//        $table->getColumns();
//
//        foreach ($table->getColumns() as $column) {
//            if (strpos($column->getName(), "photo")) {
//                $columns[] = $column->getName();
//            }
//        }
        // foreach ($columns as $key => $value) {
        //$Fdata = $adapter->query("select * from tbl_family_info where user_id='$user_id' ORDER BY id DESC", Adapter::QUERY_MODE_EXECUTE);
        // }
//        foreach ($Fdata as $F_data) {
//            foreach ($columns as $key => $value) {
//                if (empty($F_data->$value))
//                    continue;
//                else
//                    $Fphotos[] = $F_data->$value;
//            }
//        }

        foreach ($data as $P_data) {
            foreach ($P_data as $key => $value) {

                if ($key == "image_path")
                    $Pphotos[] = $value;
            }
        }

        //shuffle($Fphotos);
        shuffle($Pphotos);
        $data_gallery = $adapter->query("select * from tbl_user_gallery where user_id='$user_id' AND ref_no='$ref_no' ORDER BY id DESC", Adapter::QUERY_MODE_EXECUTE);

        $percentage = $this->userService->ProfileBar($user_id);
        $pro_per = array($percentage, $this->profileBarTemplate($percentage));

//        return new ViewModel(array("form" => $form,
//            'userSummary' => $this->userService->userSummaryById($user_id),
//            "percent" => $pro_per));

        return new ViewModel(array("Pphotos" => $Pphotos,
            "F_photos" => $Fphotos,
            "gallery_data" => $data_gallery,
            'userSummary' => $this->userService->userSummaryById($user_id),
            "percent" => $pro_per));
    }

    public function showallimagesAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $session = new Container('user');
        $user_id = $session->offsetGet('id');
        $ref_no = $session->offsetGet('ref_no');

        if ($_POST['type'] == "Personal") {
            $data = $adapter->query("select * from tbl_user_gallery where user_id='$user_id' AND ref_no='$ref_no' ORDER BY id DESC", Adapter::QUERY_MODE_EXECUTE)->toArray();
            foreach ($data as $P_data) {
                foreach ($P_data as $key => $value) {

                    if ($key == "image_path")
                        $photos[] = array($value, $P_data['id']);
                }
            }

//for testing purpose
//    		$action = $server_url = $this->getRequest()->getUri()->getScheme() . '://' . $this->getRequest()->getUri()->getHost()."/rustagi/account/delselected";
//for live purpose
            $action = $server_url = $this->getRequest()->getUri()->getScheme() . '://' . $this->getRequest()->getUri()->getHost() . "/account/delselected";

            $output[] = "<input class='btn btn-default' type='button' style='float:right;' onclick='delselected(&quot;showallimages&quot;,&quot;$action&quot;,delselectedresults)' value='delete selected'><br><br>";

            foreach ($photos as $key => $value) {
                $title = (!(int) $value[1]) ? ucwords(str_replace("_", " ", $value[1])) : "";
                $output[] = //for testing purpose
                        /* '<div class="col-sm-3"><img src="/rustagi/'.$value[0].'" onmouseover="showchck(this)" onmouseout="hidechck(this)" onclick="selectchk(this)" class="moreimgthambdeleat imghover"/>




                          <div class="deleteimg">
                          <input type="checkbox" name="delimages&#91;&#93;" value="'.$value[1].'" />
                          <input type="hidden" name="id_field" value="'.$value[1].'" />
                          </div>
                          <div class="familytitles">'.$title.'</div>
                          </div>'; */

//for live purpose

                        '<div class="col-sm-3"><img src="/' . $value[0] . '" onmouseover="showchck(this)" onmouseout="hidechck(this)" onclick="selectchk(this)" class="moreimgthambdeleat imghover"/>




    <div class="deleteimg"> 
    	<input type="checkbox" name="delimages&#91;&#93;" value="' . $value[1] . '" />
    	<input type="hidden" name="id_field" value="' . $value[1] . '" />
    </div>
    <div class="familytitles">' . $title . '</div>
    </div>';
            }
            $output[] = "<input type='hidden' name='type' value='" . $_POST['type'] . "'><input type='hidden' name='uid' value='" . $user_id . "'>";
            // echo join("",$output);
        } else {


            $metadata = new Metadata($adapter);
            $table = $metadata->getTable("tbl_family_info");
            $table->getColumns();

            foreach ($table->getColumns() as $column) {
                if (strpos($column->getName(), "photo") || strpos($column->getName(), "_name")) {
                    $columns[] = $column->getName();
                }
            }
            // foreach ($columns as $key => $value) {
            $Fdata = $adapter->query("select * from tbl_family_info where user_id='$user_id' ORDER BY id DESC", Adapter::QUERY_MODE_EXECUTE);
            // }
            foreach ($Fdata as $F_data) {
                foreach ($columns as $key => $value) {
                    if (strpos($value, "photo")) {
                        if (empty($F_data->$value))
                            continue;
                        else
                            $photos[] = array(array($F_data->$value, $value), array($F_data->$columns[$key - 1], $columns[$key - 1]));
                    }
                }
            }
//for testing purpose
//    		$action = $server_url = $this->getRequest()->getUri()->getScheme() . '://' . $this->getRequest()->getUri()->getHost()."/rustagi/account/delselected";
//for live purpose
            $action = $server_url = $this->getRequest()->getUri()->getScheme() . '://' . $this->getRequest()->getUri()->getHost() . "/account/delselected";

            $output[] = "<input class='btn btn-default' type='button' style='float:right;' onclick='delselected(&quot;showallimages&quot;,&quot;$action&quot;,delselectedresults)' value='delete selected'><br><br>";

            foreach ($photos as $key => $value) {
                $title = (!(int) $value[0][1]) ? ucwords(str_replace("_", " ", $value[0][1])) : "";
                $Name = (!(int) $value[1][0]) ? ucwords(str_replace("_", " ", $value[1][0])) : "";
                $output[] = //for testing purpose
                        /*
                          '<div class="col-sm-3"><img src="/rustagi/'.$value[0][0].'" onmouseover="showchck(this)" onmouseout="hidechck(this)" onclick="selectchk(this)" class="moreimgthambdeleat imghover"/>
                          <div class="deleteimg">
                          <input type="checkbox" name="delimages&#91;&#93;" value="'.$value[0][1].'" />
                          <input type="hidden" name="id_field" value="'.$value[0][1].'" />
                          </div>
                          <div class="familytitles">'.$title.'</div>
                          <div class="familytitles">'.$Name.'</div>
                          </div>';
                         */
//for Live Purpose
                        '<div class="col-sm-3"><img src="/' . $value[0][0] . '" onmouseover="showchck(this)" onmouseout="hidechck(this)" onclick="selectchk(this)" class="moreimgthambdeleat imghover"/>
    <div class="deleteimg"> 
    	<input type="checkbox" name="delimages&#91;&#93;" value="' . $value[0][1] . '" />
    	<input type="hidden" name="id_field" value="' . $value[0][1] . '" />
    </div>
    <div class="familytitles">' . $title . '</div>
    <div class="familytitles">' . $Name . '</div>
    </div>';
            }


            $output[] = "<input type='hidden' name='type' value='" . $_POST['type'] . "'><input type='hidden' name='uid' value='" . $user_id . "'>";
        }

        echo join("", $output);
        // echo "<pre>";
        // print_r($photos);
        die;
    }

    public function delselectedAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        if ($_POST['Itype'] == "Personal") {
            $sql = "delete from tbl_user_gallery where id IN(" . $_POST['idfield'] . ")";
        } else {
            $var = str_replace(",", "='',", $_POST['idfield']);
            $var = "" . $var . "=''";
            $sql = "update tbl_family_info set $var where user_id='" . $_POST['user_id'] . "'";
        }

        $result = $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        echo ($result) ? "deleted Successfully" : "Couldn't perform your request";
        // echo $sql;
        die;
    }

    public function AjaxImgUploadAction() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_FILES['img_file']['name'];
            $tmpName = $_FILES['img_file']['tmp_name'];
            $error = $_FILES['img_file']['error'];
            $size = $_FILES['img_file']['size'];
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            switch ($error) {
                case UPLOAD_ERR_OK:
                    $valid = true;
                    //validate file extensions
                    if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif'))) {
                        $valid = false;
                        $response = "Invalid file extension. Only( jpg, jpeg, png, gif ) are allowed";
                    }
                    //validate file size
                    if ($size / 1024 / 1024 > 2) {
                        $valid = false;
                        $response = "File size is exceeding 2MB maximum allowed size.";
                    }
                    //upload file
                    if ($valid) {
                        $bashPath = ROOT_PATH;
                        $session = new Container('user');
                        $user_id = $session->offsetGet('id');
                        $ref_no = $session->offsetGet('ref_no');
                        $user_name = $session->offsetGet('full_name');
                        $user_folder = $user_id . "__" . $user_name;
                        $name = time() . $name;
                        if (!file_exists($bashPath . "/uploads/$user_folder")) {
                            mkdir($bashPath . "/uploads/$user_folder", 0777, true);
                            $targetPath = $bashPath . "/uploads/$user_folder/" . $name;
                            $uploaded = move_uploaded_file($tmpName, $targetPath);
                        } else {
                            $targetPath = $bashPath . "/uploads/$user_folder/" . $name;
                            $uploaded = move_uploaded_file($tmpName, $targetPath);
                        }
                        if ($uploaded) {
                            $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
                            $QUERY = "UPDATE tbl_user_info set profile_photo='/uploads/$user_folder/$name' WHERE `user_id`='$user_id' AND `ref_no`='$ref_no'";
                            $user = $adapter->query($QUERY, Adapter::QUERY_MODE_EXECUTE);
                            //*****Insert in Gallery Table******
                            $adapter->query("insert into tbl_user_gallery set user_id='$user_id',ref_no='$ref_no',image_path='/uploads/$user_folder/$name',
						 img_relation='user'", Adapter::QUERY_MODE_EXECUTE);
                            $session->profile_photo = "/uploads/$user_folder/$name";
                            $response = 'File uploaded Successfully.';
                            return new JsonModel(array("Status" => "true", "message" => $response, "file_path" => "/uploads/$user_folder/$name"));
                        } else {
                            $response = "Error! File Couldn't uploaded";
                        }
                    }
                    break;
                case UPLOAD_ERR_INI_SIZE:
                    $response = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $response = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $response = 'The uploaded file was only partially uploaded.';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $response = 'No file was uploaded.';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $response = 'Missing a temporary folder.';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $response = 'Failed to write file to disk.';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $response = 'File upload stopped by extension.';
                    break;
                default:
                    $response = 'Unknown error';
                    break;
            }
            return new JsonModel(array("Status" => "false", "message" => $response));
        }
    }

    public function AjaxImgUploadGalleryAction() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $session = new Container('user');
            //print_r($_POST['cropenabled']);exit;
            if ($_POST['cropenabled'] != "Enable") {
                // if($_POST['img_relation'] !='' && $_FILES['file_upload']['name'] !=''){
                $img_relation = trim($_POST['img_relation']);
                $name = $_FILES['file_upload']['name'];
                $tmpName = $_FILES['file_upload']['tmp_name'];
                $error = $_FILES['file_upload']['error'];
                $size = $_FILES['file_upload']['size'];
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                switch ($error) {
                    case UPLOAD_ERR_OK:
                        $valid = true;
                        //validate file extensions
                        if (!in_array($ext, array('jpg', 'jpeg'))) {
                            $valid = false;
                            $response = "Invalid file extension. Only( jpg, jpeg ) are allowed";
                        }
                        //validate file size
                        if ($size / 1024 / 1024 > 2) {
                            $valid = false;
                            $response = "File size is exceeding 2MB maximum allowed size.";
                        }
                        //upload file
                        if ($valid) {

                            //$bashPath = ROOT_PATH;
                            $session = new Container('user');
                            $user_id = $session->offsetGet('id');
                            $ref_no = $session->offsetGet('ref_no');
                            $userInfo = $this->userService->getUserInfoById($user_id, array('full_name'));
                            $user_name = $userInfo->getFullName();
                            $user_folder = $user_id . "__" . $user_name;
                            $name = time() . $name;
                            //$upload=new \Zend\File\Transfer\Adapter\Http();
                            //$upload->setDestination(PUBLIC_PATH.'/uploads');
                            if (!file_exists(PUBLIC_PATH . "/uploads/$user_folder")) {
                                mkdir(PUBLIC_PATH . "/uploads/$user_folder", 0777, true);
                                $targetPath = PUBLIC_PATH . "/uploads/$user_folder/" . $name;

                                $uploaded = move_uploaded_file($tmpName, $targetPath);
                            } else {
                                $targetPath = PUBLIC_PATH . "/uploads/$user_folder/" . $name;

                                $uploaded = move_uploaded_file($tmpName, $targetPath);
                            }


                            if ($uploaded) {
                                $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
                                //*********Insert in Gallery Table******
                                $stmt = $adapter->query("insert into tbl_user_gallery set user_id='$user_id',ref_no='$ref_no',image_path='$user_folder/$name',
							 img_relation='user'", Adapter::QUERY_MODE_EXECUTE);

                                //*********Select Images to Render******
                                $data = $adapter->query("select * from tbl_user_gallery where user_id='$user_id' AND ref_no='$ref_no' ORDER BY id DESC", Adapter::QUERY_MODE_EXECUTE);
                                $response = 'File uploaded Successfully.';
                                return new JsonModel(array("Status" => "true", "message" => $response, "gallery_data" => $data));
                            } else {
                                $response = "Error! File Couldn't uploaded";
                            }
                        }
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                        $response = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $response = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $response = 'The uploaded file was only partially uploaded.';
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $response = 'No file was uploaded.';
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $response = 'Missing a temporary folder.';
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $response = 'Failed to write file to disk.';
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $response = 'File upload stopped by extension.';
                        break;
                    default:
                        $response = 'Unknown error';
                        break;
                }
                //  }else{
                // $response = 'All fields are required!';
                //  }            
                return new JsonModel(array("Status" => "false", "message" => $response));
            } else {
                $session = new Container('user');
                $user_id = $session->offsetGet('id');
                $ref_no = $session->offsetGet('ref_no');

                // $ref_no=$session->offsetGet('ref_no');
                $userInfo = $this->userService->getUserInfoById($user_id, array('full_name'));

                $user_name = $userInfo->getFullName();
                $name = time() . $_FILES['file_upload']['name'];
                $ext = strtolower(pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION));

                $original_image = $_FILES['file_upload']['tmp_name'];

                $user_folder = $user_id . "__" . $user_name . "/";

                $new_image = '/uploads/' . $user_folder . $name;

                $image_quality = '95';





// Get dimensions of the original image
                list( $current_width, $current_height ) = getimagesize($original_image);

// Get coordinates x and y on the original image from where we
// will start cropping the image, the data is taken from the hidden fields of form.
                $x1 = $_POST['x1'];
                $y1 = $_POST['y1'];
                $x2 = $_POST['x2'];
                $y2 = $_POST['y2'];
                $width = $_POST['width'];
                $height = $_POST['height'];
// print_r( $_POST ); die;
// Define the final size of the image here ( cropped image )
                $crop_width = 200;
                $crop_height = 200;
// Create our small image
                $new = imagecreatetruecolor($crop_width, $crop_height);
// Create original image
                $current_image = imagecreatefromjpeg($original_image);
// resampling ( actual cropping )
                imagecopyresampled($new, $current_image, 0, 0, $x1, $y1, $crop_width, $crop_height, $width, $height);
// this method is used to create our new image
                $result = imagejpeg($new, $new_image, $image_quality);

                if (!in_array($ext, array('jpg', 'jpeg'))) {
                    return new JsonModel(array("Status" => 0, "message" => "only jpeg files are allowed"));
                }
                //print_r($result);exit;

                if ($result) {
                    print_r('$result');
                    exit;
                    if (!file_exists("/uploads/$user_folder")) {
                        mkdir(ROOT_PATH . "/uploads/$user_folder", 0777, true);
                        $targetPath = '/uploads/' . $user_folder . $name;
                        //print_r($targetPath);
                        // exit;
                        $uploaded = move_uploaded_file($tmpName, $targetPath);
                    } else {
                        $targetPath = '/uploads/' . $user_folder . $name;
                        //print_r($targetPath);
                        //exit;
                        $uploaded = move_uploaded_file($tmpName, $targetPath);
                    }

                    $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
                    //*********Insert in Gallery Table******
                    $adapter->query("insert into tbl_user_gallery set user_id='$user_id',ref_no='$ref_no',image_path='$user_folder/$name',
							 img_relation='user'", Adapter::QUERY_MODE_EXECUTE);
                    //*********Select Images to Render******
                    $data = $adapter->query("select * from tbl_user_gallery where user_id='$user_id' AND ref_no='$ref_no' ORDER BY id DESC", Adapter::QUERY_MODE_EXECUTE);
                    $response = 'File uploaded Successfully.';
                    return new JsonModel(array("Status" => "true", "message" => $response, "gallery_data" => $data));
                } else {
                    $response = "Error! File Couldn't uploaded";
                }
            }
        }
    }

    public function ChangeProfImgFrGalleryAction() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($_POST["ImageName"] != '') {
                $ImageName = $_POST["ImageName"];
                $session = new Container('user');
                $user_id = $session->offsetGet('id');
                $ref_no = $session->offsetGet('ref_no');
                $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
                $QUERY = "UPDATE tbl_user_info set profile_photo='$ImageName' WHERE `user_id`='$user_id' AND `ref_no`='$ref_no'";
                $user = $adapter->query($QUERY, Adapter::QUERY_MODE_EXECUTE);
                $session->profile_photo = "$ImageName";
                $response = 'Profile updated Successfully.';
                return new JsonModel(array("Status" => "true", "message" => $response, "file_path" => "$ImageName"));
            } else {
                $response = 'No Image was selected!';
            }
        } else {
            $response = 'An-authorize way to upload image!';
        }
        return new JsonModel(array("Status" => "false", "message" => $response));
    }

    public function postAction() {

        $userSession = $this->getUser()->session();
        $user_id = $userSession->offsetGet('id');
        $ref_no = $userSession->offsetGet('ref_no');

        //$postcategories = $this->getPostcategoryTable()->customFields(array('id', 'category_name'));
        //PostForm::$postcategoryList = $postcategories;
        // print_r($postcategories);die;

        $form = new PostForm($this->commonService);
        $info = $this->userService->getUserPostById($user_id);
        //Debug::dump($info);
        $form->bind($info);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

//            $page = new Posts();
//            $form->setInputFilter($page->getInputFilter());
//
//            $mergedata = array_merge(
//                    $this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray()
//            );
//            // print_r($mergedata);die;
//
//            $form->setData($mergedata);
            // $data = (array) $request->getPost();
            if ($form->isValid()) {

                //Debug::dump($form->getData());
                // exit;
                //$entity = $page->exchangeArray($postform->getData());
                //unset($page->inputFilter);
                // $session = new Container('user');
                // $user_id=$session->offsetGet('id');
                // 		echo $user_id;
                $this->userService->saveUserPost($info);
//                return $this->redirect()->toRoute('application/default', array(
//                            'action' => 'post',
//                            'controller' => 'account',
//                            "id" => 1
//                ));
            }
        }

        $percentage = $this->userService->ProfileBar($user_id);
        $pro_per = array($percentage, $this->profileBarTemplate($percentage));

        return new ViewModel(array("form" => $form,
            'userSummary' => $this->userService->userSummaryById($user_id),
            "percent" => $pro_per));
    }

    /*     * ****Ajax Call***** */

    public function getStateNameAction() {
        $Request = $this->getRequest();
        if ($Request->isPost()) {
            $Country_ID = $Request->getPost("Country_ID");
            $state_name = $this->getStateTable()->getStateListByCountryCode($Country_ID);
            if (count($state_name))
                return new JsonModel(array("Status" => "Success", "statelist" => $state_name));
            else
                return new JsonModel(array("Status" => "Failed", "statelist" => NULL));
        }
    }

    /*     * ****Ajax Call***** */

    public function getCityNameAction() {
        $Request = $this->getRequest();
        if ($Request->isPost()) {
            $State_ID = $Request->getPost("State_ID");
            $city_name = $this->getCityTable()->getCityListByStateCode($State_ID);
            if (count($city_name))
                return new JsonModel(array("Status" => "Success", "statelist" => $city_name));
            else
                return new JsonModel(array("Status" => "Failed", "statelist" => NULL));
        }
    }

    public function cropimageAction() {

        //print_r($_POST);exit;
        //print_r($ref_no);
        //exit;
        if ($_POST['cropenabled'] == "Enable") {

//            $session = new Container('user');
//            $user_id = $session->offsetGet('id');
//            $ref_no = $session->offsetGet('ref_no');
//            $user_name = $session->offsetGet('full_name');

            $user_id = $_POST['uid'];
            $ref_no = $_POST['ref_no'];
            $user_name = "Unknown";
            $name = time() . $_FILES['file']['name'];
            $original_image = $_FILES['file']['tmp_name'];

            $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

            $user_folder = $user_id . "__" . $user_name;

            $new_image = PUBLIC_PATH . '/uploads/' . $user_folder . '/' . $name;
            $new_image_thumb = PUBLIC_PATH . '/uploads/' . $user_folder . '/100x100/' . $name;

            $image_quality = '95';

            if (!file_exists(PUBLIC_PATH . "/uploads/$user_folder")) {
                mkdir(PUBLIC_PATH . "/uploads/$user_folder", 0777, true);
                // $targetPath =  ROOT_PATH.'/uploads/'.$user_folder.$name;
                // $uploaded=move_uploaded_file($tmpName,$targetPath);
            }
            if (!file_exists(PUBLIC_PATH . "/uploads/$user_folder/100x100")) {
                mkdir(PUBLIC_PATH . "/uploads/$user_folder/100x100", 0777, true);
                // $targetPath =  ROOT_PATH.'/uploads/'.$user_folder.$name;
                // $uploaded=move_uploaded_file($tmpName,$targetPath);
            }

// Get dimensions of the original image
            list( $current_width, $current_height ) = getimagesize($original_image);

// Get coordinates x and y on the original image from where we
// will start cropping the image, the data is taken from the hidden fields of form.
            $x1 = $_POST['x1'];
            $y1 = $_POST['y1'];
            $x2 = $_POST['x2'];
            $y2 = $_POST['y2'];
            $width = $_POST['width'];
            $height = $_POST['height'];
            //print_r( $_POST ); die;
// Define the final size of the image here ( cropped image )
            $crop_width = 200;
            $crop_height = 200;
// Create our small image
            $new = imagecreatetruecolor($crop_width, $crop_height);

// Create original image
            $current_image = imagecreatefromjpeg($original_image);
// resampling ( actual cropping )
            imagecopyresampled($new, $current_image, 0, 0, $x1, $y1, $crop_width, $crop_height, $width, $height);
// this method is used to create our new image
            $result = imagejpeg($new, $new_image, $image_quality);


//thumb start
            $crop_width = 30;
            $crop_height = 30;
            $thumbNew = imagecreatetruecolor($crop_width, $crop_height);
            imagecopyresampled($thumbNew, $current_image, 0, 0, $x1, $y1, $crop_width, $crop_height, $width, $height);
            $thumb = imagejpeg($thumbNew, $new_image_thumb, $image_quality);

            if (!in_array($ext, array('jpg', 'jpeg'))) {
                return new JsonModel(array("Status" => 0, "message" => "only jpeg files are allowed"));
            }

            if ($result) {

                $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
                //*********Insert in Gallery Table******
                // $already = $adapter->query("select user_id from tbl_family_info where user_id=$user_id",\Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE)->count();
                // 	if($already == 0){
                // 		$adapter->query("insert into ".$_POST['table_name']."('user_id','".$_POST['field_name']."') values($user_id,'/uploads/$user_folder/$name')", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
                // 	}	
                // else 
                $statement = $adapter->query("INSERT INTO tbl_user_gallery (user_id, ref_no, image_path) 
                        VALUES ('$user_id','$ref_no','$user_folder/$name')");


                $res = $statement->execute();
                $imgid = $res->getGeneratedValue();
                //print_r($statement);
                //exit;
                //*********Select Images to Render******
                // $data=$adapter->query("select ".$_POST['field_name']." from tbl_family_info where user_id='$user_id'", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);


                $response = 'File uploaded Successfully.';
                //for testing purpose
                $imgidpath = "$user_folder/$name";

                //for Live Purpose
                // $imgidpath = "/uploads/$user_folder/$name";

                return new JsonModel(array("Status" => 1, "data" => $imgidpath, "imgid" => $imgid));
            } else {
                return new JsonModel(array("Status" => 0, "message" => "couldn't crop image some error occured"));
            }
        } else {
            //$response = $this->familyimages($_POST, $_FILES);
            $resp = $this->getResponse();
            $resp->getHeaders()->addHeaderLine('Content-Type', 'application/json');

            //$img_relation = trim($post['field_name']);
            $name = $_FILES['file']['name'];
            $tmpName = $_FILES['file']['tmp_name'];
            $error = $_FILES['file']['error'];
            $size = $_FILES['file']['size'];
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            switch ($error) {
                case UPLOAD_ERR_OK:
                    $valid = true;
                    //validate file extensions
                    if (!in_array($ext, array('jpg'))) {
                        $valid = false;
                        $response = "Invalid file extension. Only( jpg ) are allowed";
                    }
                    //validate file size
                    if ($size / 1024 / 1024 > 2) {
                        $valid = false;
                        $response = "File size is exceeding 2MB maximum allowed size.";
                    }
                    //upload file
                    if ($valid) {

                        // return $post;
                        $bashPath = PUBLIC_PATH;
                        $user_id = $_POST['uid'];
                        $ref_no = $_POST['ref_no'];
                        $user_name = "Unknown";

                        $user_folder = $user_id . "__" . $user_name;
                        $name = time() . $name;

                        $new_image = PUBLIC_PATH . '/uploads/' . $user_folder . '/' . $name;
                        $new_image_thumb = PUBLIC_PATH . '/uploads/' . $user_folder . '/100x100/' . $name;
                        $image_quality = '95';



                        if (!file_exists($bashPath . "/uploads/$user_folder")) {
                            mkdir($bashPath . "/uploads/$user_folder", 0777, true);
                            $targetPath = $bashPath . "/uploads/$user_folder/" . $name;
                            //$uploaded = move_uploaded_file($tmpName, $targetPath);
                        } else {
                            $targetPath = $bashPath . "/uploads/$user_folder/" . $name;
                            //$uploaded = move_uploaded_file($tmpName, $targetPath);
                        }

// Get dimensions of the original image
                        list( $current_width, $current_height ) = getimagesize($tmpName);

// Get coordinates x and y on the original image from where we
// will start cropping the image, the data is taken from the hidden fields of form.
                        $x1 = $_POST['x1'];
                        $y1 = $_POST['y1'];
                        $x2 = $_POST['x2'];
                        $y2 = $_POST['y2'];
                        $width = $_POST['width'];
                        $height = $_POST['height'];
//                        //print_r( $_POST ); die;
// Define the final size of the image here ( cropped image )
                        $crop_width = 200;
                        $crop_height = 200;
// Create our small image
                        $new = imagecreatetruecolor($crop_width, $crop_height);

// Create original image
                        $current_image = imagecreatefromjpeg($tmpName);
// resampling ( actual cropping )
                        imagecopyresampled($new, $current_image, 0, 0, $x1, $y1, $crop_width, $crop_height, $width, $height);
// this method is used to create our new image
                        $result = imagejpeg($new, $new_image, $image_quality);


//thumb start
                        $crop_width = 30;
                        $crop_height = 30;
                        $thumbNew = imagecreatetruecolor($crop_width, $crop_height);
                        $current_image = imagecreatefromjpeg($tmpName);
                        imagecopyresampled($thumbNew, $current_image, 0, 0, $x1, $y1, $crop_width, $crop_height, $width, $height);
                        $thumb = imagejpeg($thumbNew, $new_image_thumb, $image_quality);



                        //exit;
                        if ($uploaded) {


                            $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
                            //*********Insert in Gallery Table******
                            $statement = $adapter->query("INSERT INTO tbl_user_gallery (user_id, ref_no, image_path) 
                        VALUES ('$user_id','$ref_no','$user_folder/$name')");
                            $res = $statement->execute();
                            $imgid = $res->getGeneratedValue();
                            //$adapter->query("update " . $post['table_name'] . " set " . $post['field_name'] . "='/uploads/$user_folder/$name' where user_id=$user_id ", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
                            //*********Select Images to Render******
                            // $data=$adapter->query("select ".$post['field_name']." from tbl_family_info where user_id='$user_id'", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
                            $response = 'File uploaded Successfully.';
                            // return $data;
//for testing purpose		
                            $imgidpath = "$user_folder/$name";
                            $resp->setContent(json_encode(array("Status" => 1, "data" => $imgidpath, "imgid" => $imgid)));
//for live purpose
// $imgidpath = "/uploads/$user_folder/$name";
                            //        $resp->setContent(json_encode(array("Status"=>1,"data"=>$imgidpath,"imgid"=>$post['field_name'])));

                            return $resp;

                            // return new JsonModel(array("Status"=>1,"data"=>$targetPath));
                            // return new JsonModel(array("Status"=>"true","message"=>$response,"family_data"=>$data));							
                        } else {
                            $response = "Error! File Couldn't uploaded";
                        }
                    }
                    break;
                case UPLOAD_ERR_INI_SIZE:
                    $response = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $response = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $response = 'The uploaded file was only partially uploaded.';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $response = 'No file was uploaded.';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $response = 'Missing a temporary folder.';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $response = 'Failed to write file to disk.';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $response = 'File upload stopped by extension.';
                    break;
                default:
                    $response = 'Unknown error';
                    break;
            }

            $resp->setContent(json_encode(array("Status" => 0, "message" => $response)));
            return $resp;
        }

        //exit;
    }

    public function covertdateageAction() {
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');

        $today = date("Y-m-d");
        $dob = $this->convertdate($_POST['value']);

        $years = $this->valdateselection(strtotime($today), strtotime($dob));

        if ($years < '15') {
            $msg = "your age should be greater than 15";
            $respArr = array("status" => 0, "content" => $msg);
        } else
            $respArr = array("status" => 1, "content" => $years);


        $response->setContent(json_encode($respArr));
        return $response;
    }

    public function convertdate($date) {

        $timestamp = strtotime($date);
        $date = date("Y-m-d", $timestamp);
        return $date;
    }

    public function valdateselection($today, $dob) {
        $diff = $today - $dob;
        $years = floor($diff / (365 * 60 * 60 * 24));
        // $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        // $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
        return $years;
    }

    public function profileBarTemplate($percentage) {

        $view = new PhpRenderer();
        $resolver = new TemplateMapResolver();
        $resolver->setMap(array(
            'mailTemplate' => __DIR__ . '/../../../view/application/profileBar/profileBar.phtml'
        ));
        $view->setResolver($resolver);

        $viewModel = new ViewModel();
        $viewModel->setTemplate('mailTemplate')->setVariables(array(
            'percentage' => $percentage,
        ));
        $message = $view->render($viewModel);
        //Debug::dump($message);
        //exit;
        return $message;
    }

}

?>
