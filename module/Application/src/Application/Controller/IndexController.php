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
        if ($this->params()->fromRoute('id') == 1) {
            $msg = "Message sent successfully";
        } else
            $msg = "";

        $contactform = new \Application\Form\ContactForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $page = new \Application\Model\Entity\Contact();
            $contactform->setInputFilter($page->getInputFilter());
            $contactform->setData($request->getPost());
            $data = (array) $request->getPost();
            if ($contactform->isValid()) {
                $page->exchangeArray($data);
                unset($page->inputFilter);
                // print_r($page); exit;
//                  // $this->renderer = $this->getServiceLocator()->get('ViewRenderer');  
                $content = "<table border=1>
<tbody>
<tr>
<td>Name</td>
<td>'" . $page->name . "'</td>
</tr>
<tr>
<td>Phone Number</td>
<td>" . $page->phone_no . "</td>
</tr>
<tr>
<td>Email</td>
<td>" . $page->email . "</td>
</tr>
<tr>
<td>Message</td>
<td>" . $page->message . "</td>
</tr>
</tbody>
</table>";



                $this->mailsetup($content);


                $id = $this->getContactTable()->saveContact($page);
                return $this->redirect()->toRoute('application/default', array(
                            'action' => 'contact',
                            'controller' => 'pages',
                            "id" => 1
                ));
            }
        }

        $sql = "select * from tbl_rustagi_institutions";

        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        /*         * ****Fetch all Members Data from db******** */
        $InstData = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        $CommunityData = array();

        foreach ($InstData as $idata) {

            $result[] = $idata;
            $MemberData = $adapter->query("select * from tbl_rustagi_institutions_members where institute_id='" . $idata->id . "'", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
            if (count($MemberData) > 0) {
                $CommunityData[$idata->id] = $MemberData;
            }
        }


        $filters_data = $this->sidebarFilters();

        return new ViewModel(array('InstData' => $result, 'CommunityData' => $CommunityData, "filters_data" => $filters_data, "form" => $contactform, "message" => $msg));
    }

    public function galleryAction() {
        return new ViewModel();
    }
    
     public function sidebarFilters()
    {
       $adapter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
       // $select=$this->getServiceLocator()->get('Zend\Db\sql\Expression');

        $filters_array = array("country"=>"tbl_country","city"=>"tbl_city","state"=>"tbl_state");

        foreach($filters_array as $key =>$table){

            $filters_data[$key] = $adapter->query("select * from ".$table."", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        }
        return $filters_data;
    }

}

?>