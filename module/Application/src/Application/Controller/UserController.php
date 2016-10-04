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

    public function loginUserAction() {

        if ($this->getRequest()->isPost()) {


            $request = $this->getRequest();
            //Debug::dump($request = $this->getRequest());
            //exit;
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
                    $userInfo=$this->userService->getUserInfoById($userSession->offsetGet('id'), array('full_name'));
                    $userSession->offsetSet('full_name', $userInfo->getFullName());
                    //Debug::dump($storage->read());
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


            if ($auth->hasIdentity() && in_array($auth->getIdentity()->role, array('user'))) {

                //\Zend\Debug\Debug::dump($userSession->offsetGet('id'));
                // exit;
                return $this->redirect()->toRoute('profile');
                //return $this->redirect()->toUrl($this->getRequest()->getHeader('Referer')->getUri());
            }
        }
    }

    public function loginAction() {
        //Debug::dump($this->checkLogin());
        if ($this->getRequest()->isPost()) {


            $request = $this->getRequest();
            //Debug::dump($request = $this->getRequest());
            //exit;
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
                     $userInfo=$this->userService->getUserInfoById($userSession->offsetGet('id'), array('full_name'));
                    $userSession->offsetSet('full_name', $userInfo->getFullName());
                    //Debug::dump($storage->read());
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

            if ($auth->hasIdentity() && in_array($auth->getIdentity()->role, array('user'))) {

                return $this->redirect()->toRoute('profile');
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
                //Debug::dump($signupform->getData());
                //exit;


                $userInfoObject = $this->userService->saveUserSignUp($signupform->getData());
                //Debug::dump($userInfoObject);
                //exit;
                $number = $signupform->getData()->getMobileNo();
                $code = rand(1111, 9999);
                date_default_timezone_set('Asia/Kolkata');
                $time = date('H:i');
                $this->userService->saveAcitivationSmsCode($userInfoObject->getUserId(), $number, $code, $time);
                $this->sendAccountThanksSms($userInfoObject->getUsername(), $userInfoObject->getMobileNo(), $code);
                //$this->sendAccountActivationEmail($userInfoObject->getUsername(), $userInfoObject->getFullName(), $userInfoObject->getEmail(), $userInfoObject->getActivationKey());
                //Debug::dump($userInfoObject);
                //exit;
                //header("Location:$base"."user/confimotpsignup?userid=$id&number=$number&code=$code&time=$time");
                //********Redirect *********
                return $this->redirect()->toRoute('user', array('action' => 'confimotpsignup'));
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

    public function confimotpsignupAction() {
        //Debug::dump($this->getRequest()->getQuery());

        $data['userid'] = (int) $this->getRequest()->getQuery('userid');
        $data['number'] = (int) $this->getRequest()->getQuery('number');
        $data['code'] = (int) $this->getRequest()->getQuery('code');
        $data['time'] = $this->getRequest()->getQuery('time');

        return new ViewModel(array("data" => $data));
        // echo $active ; die;
    }

    public function confirmotpAction() {
        $isreg = $_POST['is_reg'];
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $arrdef = $adapter->query("select * from tbl_mobile where (code=" . $_POST['otp'] . " && mobile=" . $_POST['number'] . " && time='" . $_POST['time'] . "')", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $size = $arrdef->count();

        $uid = $arrdef->toArray()[0]['user_id'];

        // echo $uid;die;
        // foreach ($arrdef as $user) {
        //         $userid = $user->user_id;
        //     }

        if ($isreg == 1) {

            $update = $this->getUserTable()->activateUser($uid);
            if ($update)
                $msg = "Congratulations Registration successful You can proceed to login";
            else
                $msg = "Their is some internal error occured . Please try later";
        }

        $succarr = array("userid" => $userid, "is_reg" => $isreg, "msg" => $msg);

        if ($size == 1) {
            return new JsonModel(array("resp" => 1, "success" => $succarr));

            // $response = $this->getResponse();
            // $response->getHeaders()->addHeaderLine( 'Content-Type', 'application/json' );
            // $response->setContent(json_encode(array("resp"=>1,"success"=>$succarr)));
            //     return $response;
        } else {
            return new JsonModel(array("resp" => 0, "error" => "otp doesn't match"));

            // $response = $this->getResponse();
            // $response->getHeaders()->addHeaderLine( 'Content-Type', 'application/json' );
            // $response->setContent(json_encode(array("resp"=>0,"error"=>"otp doesn't match")));
            //     return $response;
        }
        exit();
    }

}

?>