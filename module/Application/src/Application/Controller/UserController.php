<?php

namespace Application\Controller;

use Application\Form\SignupForm;
use Application\Service\UserServiceInterface;
use Common\Service\CommonServiceInterface;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Db\Adapter\Adapter;
use Zend\Debug\Debug;
use Zend\Mime\Part;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class UserController extends AppController {

    protected $userService;
    protected $commonService;
    protected $hydrator;

    public function __construct(CommonServiceInterface $commonService, UserServiceInterface $userService) {
        $this->userService = $userService;
        $this->commonService = $commonService;
        $this->hydrator = new ClassMethods();
    }

    public function indexAction() {


        var_dump($this->userService->checkAlreadyExist('email', 'satya.comnet@gmail.com'));

        exit;
        //return new ViewModel();
    }

    public function loginAction() {

        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest();
            //$login_email = $request->getPost('login_email');
            //$login_password = md5($request->getPost('login_password'));

            $username = $request->getPost('login_email');
            $password = $request->getPost('login_password');
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $authAdapter = new AuthAdapter($dbAdapter);
            $authAdapter->setTableName('tbl_user');
            $authAdapter->setIdentityColumn('email');
            $authAdapter->setIdentityColumn('mobile_no');
            $authAdapter->setCredentialColumn('password');
            $authAdapter->setCredentialTreatment('md5(?)');
            $authAdapter->setIdentity($username)->setCredential($password);
            $auth = new AuthenticationService();
            $result = $auth->authenticate($authAdapter);
            //Debug::dump($result->getCode());
            //exit;
            switch ($result->getCode()) {
                case Result::FAILURE_IDENTITY_NOT_FOUND:
                    // do stuff for nonexistent identity

                    break;

                case Result::FAILURE_CREDENTIAL_INVALID:
                    // do stuff for invalid credential
                    break;

                case Result::SUCCESS:

                    $storage = $auth->getStorage();
                    $storage->write($authAdapter->getResultRowObject(
                                    null, 'password'
                    ));

                    //\Zend\Debug\Debug::dump($storage->read());
                    //exit;
                    $userSession = $this->getUser()->session();
                    $userSession->user = $storage->read();
                    foreach ($storage->read() as $u => $v) {
                        $userSession->offsetSet($u, $v);
                    }
                    Debug::dump($storage->read());
                    //exit;
                    $time = 1209600; // 14 days 1209600/3600 = 336 hours => 336/24 = 14 days
                    //if ($data['rememberme']) $storage->getSession()->getManager()->rememberMe($time); // no way to get the session

                    if ($request->getPost('rememberme')) {
                        $sessionManager = new SessionManager();
                        $sessionManager->rememberMe($time);
                    }
                    break;

                default:
                    // do stuff for other failure
                    break;
            }
            $messages = '';
            foreach ($result->getMessages() as $message) {
                $messages .= "$message\n";
            }


            if ($auth->getIdentity()) {

                //\Zend\Debug\Debug::dump($userSession->offsetGet('id'));
                // exit;

                return $this->redirect()->toUrl($this->getRequest()->getHeader('Referer')->getUri());
            }
        }
    }

    public function LogoutAction() {

        $auth = new AuthenticationService();
        $auth->clearIdentity();
        $session = new Container('user');
        $session->getManager()->getStorage()->clear('user');

        return $this->redirect()->toRoute('home');
    }

    public function signupAction() {

        $signupform = new SignupForm($this->commonService);
        $request = $this->getRequest();
        if ($request->isPost()) {
            //$signupFilter = new SignupFormFilter();
            //$signupform->setInputFilter($signupFilter->getInputFilter());
            $signupform->setData($request->getPost());

            if ($signupform->isValid()) {



                $userInfoObject = $this->userService->saveUserSignUp($signupform->getData());
                Debug::dump($userInfoObject);
                //exit;
                $number = $signupform->getData()->getMobileNo();
                $code = rand(1111, 9999);
                date_default_timezone_set('Asia/Kolkata');
                $time = date('H:i');
                $this->userService->saveAcitivationSmsCode($userInfoObject->getUserId(), $number, $code, $time);
                $this->sendAccountThanksSms($userInfoObject->getUsername(), $userInfoObject->getMobileNo(), $code);
                $this->sendAccountActivationEmail($userInfoObject->getUsername(), $userInfoObject->getFullName(), $userInfoObject->getEmail(), $userInfoObject->getActivationKey());
                Debug::dump($userInfoObject);
                exit;
                if ($userInfoObject->getId()) {
                    $this->userService->saveUserInfo($userInfoObject);
                }


                $page->exchangeArray($data);
                unset($page->inputFilter);
                $SaveUserData["id"] = $page->id;
                $SaveUserData["user_type_id"] = $page->user_type_id;
                $SaveUserData["username"] = $page->username;
                $SaveUserData["password"] = $page->password;
                $SaveUserData["mobile_no"] = $page->mobile_no;
                $SaveUserData["email"] = $page->email;

                $act_code = md5($page->email);
                $id = $this->getUserTable()->saveUser($SaveUserData, $act_code);
                //********User Mail********
                if ($id > 0) {
                    /*                     * ****Generate User Unique Reference Number************ */
                    $dateYear = date('y');
                    if ($dateYear > 26) {
                        $dateYear = $dateYear - 26;
                        $dateYear = 64 + $dateYear;
                        $dateYear = chr($dateYear);
                        $dateYear = "A" . $dateYear;
                    } else {
                        $dateYear = 64 + $dateYear;
                        $dateYear = chr($dateYear);
                    }
                    $full_nameArray = explode(' ', $page->full_name);
                    if (count($full_nameArray) > 1) {
                        $first = strtoupper(substr($full_nameArray[0], 0, 1));
                        $last = strtoupper(substr($full_nameArray[1], 0, 1));
                        $referenceNo = $dateYear . $first . $last . $id;
                    } else {
                        $first = strtoupper(substr($full_nameArray[0], 0, 2));
                        $referenceNo = $dateYear . $first . $id;
                    }
                    /*                     * ************end generate reference number and update to userTable**************** */
                    $adapter->query("UPDATE tbl_user SET ref_no='$referenceNo' where id='$id'", Adapter::QUERY_MODE_EXECUTE);
                    $msg = "Account Created Successfully";
                    $this->getEmailLogsTable()->saveEmailLogs($id, $msg);
                    $user = $this->getUserTable()->select(array('id' => (int) $id));
                    foreach ($user as $currentUser)
                        ;
                    $SaveUserInfo["id"] = $page->id;
                    $SaveUserInfo["user_id"] = $id;
                    $SaveUserInfo["ref_no"] = $currentUser->ref_no;
                    $SaveUserInfo["user_type_id"] = $page->user_type_id;
                    $SaveUserInfo["name_title_user"] = $page->name_title_user;
                    $SaveUserInfo["full_name"] = $page->full_name;
                    $SaveUserInfo["gender"] = $page->gender;
                    $SaveUserInfo["native_place"] = $page->native_place;
                    $SaveUserInfo["gothra_gothram"] = $page->gothra_gothram;
                    $SaveUserInfo["address"] = $page->address;
                    $SaveUserInfo["country"] = $page->country;
                    $SaveUserInfo["state"] = $page->state;
                    $SaveUserInfo["city"] = $page->city;
                    $SaveUserInfo["profession"] = $page->profession;
                    $SaveUserInfo["gothra_gothram_other"] = $page->gothra_gothram_other;
                    $SaveUserInfo["branch_ids_other"] = $page->rustagi_branch_other;
                    $SaveUserInfo["profession_other"] = $page->profession_other;
                    $SaveUserInfo["branch_ids"] = $page->rustagi_branch;

                    $SaveFamilyInfo["id"] = $page->id;
                    $SaveFamilyInfo["user_id"] = $id;
                    $SaveFamilyInfo["name_title_father"] = $page->name_title_father;
                    $SaveFamilyInfo["father_name"] = $page->father_name;

                    $LastId = $this->getUserInfoTable()->saveUserInfo($SaveUserInfo);

                    $lastFamilyId = $this->getFamilyInfoTable()->savefamilyInfo($SaveFamilyInfo);
                    $objmail = new Message2();
                    //$bodyPart = new \Zend\Mime\Message(); 
                    //$bodyMessage = new \Zend\Mime\Part($body);
                    //$bodyMessage->type = 'text/html';
                    //$bodyPart->setParts(array($bodyMessage));
                    $bodyPart = "Hi " . $page->email . ",<br/><br/>" .
                            "You have Successfully Registered. Here below is your activation link.<br/>" .
                            "<strong>Please Copy and paste the link below in browser </strong> to activate your account.<br/><br/>";
                    $bodyPart.="<a href='$base" . "user/activate?active=0&user=$id&token=$act_code'>'$base" . "user/activate?active=0&user=$id&token=$act_code'</a>";
                    $bodyPart.="<br/><br/>";
                    $bodyPart.="Thanks & Regards<br/>";
                    $bodyPart.="Rustagi Samaj Team<br/>";
                    $bodyPart.="<br/><br/>";


                    $number = $SaveUserData["mobile_no"];
                    $code = rand(1111, 9999);
                    date_default_timezone_set('Asia/Kolkata');
                    $time = date('H:i');

                    // $adapter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
                    $adapter->query(" insert into `tbl_mobile`(`user_id`, `mobile`, `time`, `code`) VALUES ($id,$number,'" . $time . "',$code)", Adapter::QUERY_MODE_EXECUTE);

                    // $arrdef =  $adapter->query("select * from tbl_sms_template where msg_sku='forgot_password'", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);


                    $msg_query = $adapter->query("SELECT * FROM tbl_sms_template WHERE msg_sku='welcome_msg'", Adapter::QUERY_MODE_EXECUTE)->toArray();
                    $msg_query = $msg_query[0];
                    // $otpmsg = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)?)@', '<a href="$1">$1</a>', "<a href='/rustagi/user/activate?active=0&user=$id&token=$act_code'>'$base/rustagi/user/activate?active=0&user=$id&token=$act_code'</a>");
                    // $otpmsg = "$base"."user/activate?active=0&user=$id&token=$act_code";
                    // $otpmsg = addslashes("<a href='$base/rustagi/user/activate?active=0&user=$id&token=$act_code'>'$base/rustagi/user/activate?active=0&user=$id&token=$act_code'</a>");
                    $array = explode('<variable>', $msg_query['message']);
                    $array[0] = $array[0] . $number;
                    $array[1] = $array[1] . $code;
                    $text = urlencode(implode("", $array));

                    file_put_contents("mssg.txt", $text);
                    // echo $text;die;	
                    $url = "http://push3.maccesssmspush.com/servlet/com.aclwireless.pushconnectivity.listeners.TextListener?userId=helloalt&pass=helloalt&appid=helloalt&subappid=helloalt&msgtype=1&contenttype=1&selfid=true&to=$number&from=Helocb&dlrreq=true&text=$text&alert=1";
                    file_get_contents($url);


                    // echo $text;die;
                    // $msg_query = $adapter->query("SELECT * FROM tbl_sms_template WHERE msg_sku='welcome_msg'", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE)->toArray();
                    // $sql="SELECT * FROM tbl_sms_template WHERE msg_sku='welcome_msg'"; //die;
                    //     	$res = mysqli_query($this->con,$sql);
                    //  $msg_query=mysqli_fetch_array($res); 
                    // 	    $userEmailId = $SaveUserData["mobile_no"];
                    // $array=explode('<variable>',$msg_query['message']);
                    // $array[0]=$array[0].$userEmailId;
                    // $array[1]=$array[1]."<a href='$base/rustagi/user/activate?active=0&user=$id&token=$act_code'>'$base/rustagi/user/activate?active=0&user=$id&token=$act_code'</a>";
                    // $text=  urlencode(implode("",$array));
                    // file_put_contents("mssg.txt",$text);
                    // // echo $text;die;	
                    // $url="http://push3.maccesssmspush.com/servlet/com.aclwireless.pushconnectivity.listeners.TextListener?userId=helloalt&pass=helloalt&appid=helloalt&subappid=helloalt&msgtype=1&contenttype=1&selfid=true&to=$userEmailId&from=Helocb&dlrreq=true&text=$text&alert=1";
                    // file_get_contents($url); 
                    // /// Code for SMS Going to the particular user Ends here ////
                    // print_r($SaveUserInfo);die;
                    //echo $bodyPart;exit;
                    /* $objmail->setBody($bodyPart);
                      $objmail->setFrom("info@rustagisamaj.com","Neeraj Rustagi");
                      $objmail->addTo($page->email, $page->email);
                      $objmail->setSubject("Activate Account");
                      $objmail->setEncoding('UTF-8');
                      $transportObj = new Mail\Transport\Sendmail();
                      $transportObj->send($objmail); */
                    // $headers = "MIME-Version: 1.0" . "\r\n";
                    // $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    // $headers .= 'From: <admin@rustagisamaj.com>' . "\r\n";
                    // $headers .= 'Cc: admin@rustagisamaj.com' . "\r\n";
                    // $subject='Activate Rustagi Samaj Account';
                    // // mail($page->email,$subject,$bodyPart,$headers);	
                    // $this->sendmail($bodyPart,$page->email,"admin@rustagisamaj.com",$subject);
                    // echo "dfg";die;			
                    // return $this->redirect()->toRoute('application/default', array(
                    // 		'action' => 'index',
                    // 		'controller' => 'index'
                    // ));
                    // $succarr = array("userid"=>$userid,"time"=>$time,"mobile"=>$number,"code"=>$code);

                    header("Location:$base" . "user/confimotpsignup?userid=$id&number=$number&code=$code&time=$time");

                    echo "nahi hua";
                    die;
                }
                //********Redirect *********
                return $this->redirect()->toRoute('application/default', array(
                            'action' => 'index',
                            'controller' => 'index'
                ));
            }
        }

        return new ViewModel(array("form" => $signupform));
    }

    /*     * ****Ajax Call***** */

    public function getStateNameAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $countryId = $request->getPost("Country_ID");
            $stateName = $this->commonService->getStateListByCountryCode($countryId);
        }
        if (count($stateName)) {
            return new JsonModel(array("Status" => "Success", "statelist" => $stateName));
        } else {
            return new JsonModel(array("Status" => "Failed", "statelist" => null));
        }
    }

    /*     * ****Ajax Call***** */

    public function getCityNameAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $stateId = $request->getPost("State_ID");
            $cityName = $this->commonService->getCityListByStateCode($stateId);
            if (count($cityName))
                return new JsonModel(array("Status" => "Success", "statelist" => $cityName));
            else
                return new JsonModel(array("Status" => "Failed", "statelist" => null));
        }
    }

    /*     * ****Ajax Call***** */

    public function getRustagiBranchAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $cityId = $request->getPost("City_ID");
            $branchName = $this->commonService->getBrachListByCity($cityId);
            if (count($branchName)) {
                return new JsonModel(array("Status" => "Success", "statelist" => $branchName));
            } else {
                return new JsonModel(array("Status" => "Failed", "statelist" => NULL));
            }
        }
    }

    /*     * ****Ajax Call***** */

    public function checkAlreadyExistAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $fieldName = $request->getPost("id");
            $value = $request->getPost("value");
            $size = $this->userService->checkAlreadyExist($fieldName, $value);
        }
        if ($size == 0) {
            return new JsonModel(array("id" => $fieldName, "message" => "<p style='color:green;'>Available</p>"));
        } else {
            return new JsonModel(array("id" => $fieldName, "message" => "<p style='color:red;'>Aleardy Exists</p>"));
        }
        exit();
    }

    private function sendAccountActivationEmail($username, $name, $email, $acivationCode) {
        //$keycode = $this->generateRandomString(32);
        $view = new \Zend\View\Renderer\PhpRenderer();
        $resolver = new \Zend\View\Resolver\TemplateMapResolver();
        $resolver->setMap(array(
            'mailTemplate' => __DIR__ . '/../../../view/application/mails/accountActivationEmail.phtml'
        ));
        $view->setResolver($resolver);

        $viewModel = new ViewModel();
        $viewModel->setTemplate('mailTemplate')->setVariables(array(
            'username' => $username,
            'name' => $name,
            'email' => $email,
            'activationCode' => $acivationCode,
                //'keycode' => $keycode,
        ));

        $bodyPart = new \Zend\Mime\Message();
        $bodyMessage = new Part($view->render($viewModel));
        $bodyMessage->type = 'text/html';
        $bodyPart->setParts(array($bodyMessage));
        $message = new \Zend\Mail\Message();
        $message->addFrom('noreply@infinitescript.com', 'IT Training Platform')
                ->addTo($email)
                ->setSubject('Rustagi Account Activation')
                ->setBody($bodyPart)
                ->setEncoding('UTF-8');
        $transport = new \Zend\Mail\Transport\Sendmail();
        $transport->send($message);
    }

    private function sendAccountThanksSms($username, $mobileNumber, $code) {
        //$mobileNumber='8527075535';
        //$keycode = $this->generateRandomString(32);
        $view = new \Zend\View\Renderer\PhpRenderer();
        $resolver = new \Zend\View\Resolver\TemplateMapResolver();
        $resolver->setMap(array(
            'mailTemplate' => __DIR__ . '/../../../view/application/sms/accountThanksSms.phtml'
        ));
        $view->setResolver($resolver);

        $viewModel = new ViewModel();
        $viewModel->setTemplate('mailTemplate')->setVariables(array(
            'username' => $username,
            'code' => $code,
            'username' => 'satya',
            'code' => '5467',
                //'keycode' => $keycode,
        ));
//\Zend\Debug\Debug::dump($view->render($viewModel));
//exit;
        $message = $view->render($viewModel);
        file_put_contents("thanksSms.txt", $message);
        // echo $text;die;	
        $url = "http://push3.maccesssmspush.com/servlet/com.aclwireless.pushconnectivity.listeners.TextListener?userId=helloalt&pass=helloalt&appid=helloalt&subappid=helloalt&msgtype=1&contenttype=1&selfid=true&to=$mobileNumber&from=Helocb&dlrreq=true&text=$message&alert=1";
        file_get_contents($url);
//\Zend\Debug\Debug::dump(file_get_contents($url));
    }

}

?>