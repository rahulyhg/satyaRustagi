<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

ini_set("display_errors", 1);

class IndexController extends AppController {

    protected $indexService;

    public function __construct(\Application\Service\IndexServiceInterface $indexService) {
        $this->indexService = $indexService;
    }

    public function indexAction() {
        
        //$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        /*         * ****Fetch all Members Data from db******** */
//        $GroomData = $adapter->query("select tbl_user.id as uid,tbl_city.*,tbl_user_info.profile_photo,tbl_user_info.full_name,tbl_user_info.age,tbl_user_info.height,tbl_user_info.city,tbl_user_info.address,tbl_user_info.about_yourself_partner_family,tbl_family_info.father_name,tbl_profession.profession from tbl_user 
//		 INNER JOIN tbl_user_info on tbl_user_info.user_id=tbl_user.id
//		 INNER JOIN tbl_city on tbl_user_info.city=tbl_city.id
//		 INNER JOIN tbl_family_info on tbl_user.id=tbl_family_info.user_id
//         INNER JOIN tbl_profession on tbl_user_info.profession=tbl_profession.id
//         INNER JOIN tbl_user_roles on tbl_user_info.user_id=tbl_user_roles.user_id	
//		 		 
//		 where tbl_user_roles.IsMember='1'  ORDER BY tbl_user.id DESC", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);



        /*         * ****Return to View Model******** */
//        $date = date('Y-m-d h:i:s');
//        $Upcoming_events = $adapter->query("select * from tbl_upcoming_events where (event_date>'" . $date . "' && IsActive=1) ORDER BY id DESC", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
//        $ExecMembers = $adapter->query("select tbl_communities.category_name,tbl_user_info.user_id as uid,tbl_user.id,tbl_city.*,tbl_user_info.profile_photo,tbl_user_info.full_name,tbl_user_info.age,tbl_user_info.height,tbl_user_info.city,tbl_user_info.address,tbl_user_info.about_yourself_partner_family,tbl_family_info.father_name,tbl_profession.profession from tbl_user 
//		 INNER JOIN tbl_user_info on tbl_user_info.user_id=tbl_user.id
//		 INNER JOIN tbl_city on tbl_user_info.city=tbl_city.id
//		 INNER JOIN tbl_family_info on tbl_user.id=tbl_family_info.user_id
//         INNER JOIN tbl_profession on tbl_user_info.profession=tbl_profession.id		 
//		 INNER JOIN tbl_user_roles on tbl_user_info.user_id=tbl_user_roles.user_id
//		 INNER JOIN tbl_communities on tbl_user_info.comm_mem_id = tbl_communities.id		 
//		 where tbl_user_roles.IsExecutive='1'  ORDER BY tbl_user.id DESC", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        // foreach ($ExecMembers as $exe) {
        // 	echo $exe->uid;
        // }
        // print_r($ExecMembers);die;

        $this->flashMessenger()->addMessage(array('success' => 'Custom success message to be here...'));

        return new ViewModel(array("GroomData" => $this->indexService->getGroomData(), "Upcoming_events" => $this->indexService->getUpcomingEvents(), "ExecMembers" => $this->indexService->getExecutiveMember()));
    }

    public function viewProfileAction() {

        $param = $this->params()->fromRoute('param', false);
        echo $param;

        return new ViewModel();
    }

    public function aboutAction() {
        return new ViewModel();
    }

    public function historyAction() {

        return new ViewModel();
    }

    public function communitiesAction() {
        return new ViewModel();
    }

    public function visionAction() {
        return new ViewModel();
    }

    public function contactAction() {
        return new ViewModel();
    }

    public function galleryAction() {
        return new ViewModel();
    }

}

?>