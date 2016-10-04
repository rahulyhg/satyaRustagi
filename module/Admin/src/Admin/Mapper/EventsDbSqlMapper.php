<?php

namespace Admin\Mapper;

use Admin\Model\Entity\Events;
//use Admin\Model\Entity\Newscategories;
//use Admin\Model\Entity\Countries;
//use Admin\Model\Entity\States;
//use Admin\Model\Entity\Cities;
//use Admin\Model\Entity\Religions;
//use Admin\Model\Entity\Gothras;
//use Admin\Model\Entity\Starsigns;
//use Admin\Model\Entity\Zodiacsigns;
//use Admin\Model\Entity\Professions;
//use Admin\Model\Entity\Designations;
//use Admin\Model\Entity\Educationlevels;
//use Application\Model\Entity\UserInfo;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Update;
use Zend\Debug\Debug;
use Zend\Stdlib\Hydrator\ClassMethods;

class EventsDbSqlMapper implements EventsMapperInterface {

    protected $dbAdapter;
    protected $resultSet;
    protected $hydrator;

    public function __construct(AdapterInterface $dbAdapter) {


        $this->dbAdapter = $dbAdapter;
        $this->resultSet = new ResultSet();
        $this->hydrator = new ClassMethods();
    }

    public function getAmmir() {
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_user_info");
        $result = $statement->execute();
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new UserInfo());
        }
    }

    public function getAmmirById($id) {
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_user_info WHERE user_id=:klm");
        $parameters = array(
            'klm' => $id
        );
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new UserInfo());
        }
    }
    
    //added by amir
    
    public function test() {
        
        $data = 'hello world';
        
       return $data;
    }
    
    
    public function customFieldsCity() {        
        
        $statement = $this->dbAdapter->query("SELECT id,city_name FROM tbl_city WHERE 1");
        
        $result = $statement->execute();
        
        foreach ($result as $list) {
            $citynamelist[$list['id']] = $list['city_name'];
        }
        
        return $citynamelist;        
   
    }
    //Events
    
    
    public function getEventsList() {
//            Debug::dump($status);
//        exit;
        //if(isset($status)){
        $statement = $this->dbAdapter->query("SELECT tbl_upcoming_events.*,tbl_country.country_name,tbl_city.city_name FROM `tbl_upcoming_events` LEFT JOIN 

tbl_country ON(tbl_upcoming_events.country_id=tbl_country.id)  LEFT JOIN tbl_city

ON(tbl_upcoming_events.city_id=tbl_city.id);");
        
        $result = $statement->execute();
        //}
        // if(isset($status)){
//        Debug::dump($status);
//        exit;
//        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_field  WHERE is_active=:is_active");
//        $parameters = array(
//            'is_active' => $status,
//        );
        //$result = $statement->execute($parameters);
        //$result = $statement->execute();
        //}
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Events());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
    public function saveEvents($eventsObject) {
//         echo  "<pre>";
//                 print_r($eventsObject);
//                exit;
//         Debug::dump($eventsObject);
//            exit;
        $eventsData = $this->hydrator->extract($eventsObject);
        //print_r($educatioData);
        //exit;
        unset($eventsData['id']); // Neither Insert nor Update needs the ID in the array

        if ($eventsObject->getId()) {
//            echo  "<pre>";
//            echo  "hello";exit;
            $statement = $this->dbAdapter->query("UPDATE tbl_upcoming_events 
                SET event_name=:event_name,
                    sponser_name=:sponser_name,
                    event_organiser=:event_organiser,
                    sponser_photo=:sponser_photo,  
                    organiser_photo=:organiser_photo,
                    organiser_contact=:organiser_contact,
                    sponser_contact=:sponser_contact,
                    is_active=:is_active,
                    event_desc=:event_desc,
                    image=:image,
                    country_id=:country_id,
                    state_id=:state_id,
                    city_id=:city_id,
                    event_date=:event_date,
                    end_date=:end_date,
                    start_time=:start_time,
                    end_time=:end_time,
                    venue=:venue,
                    event_cost=:event_cost,
                    event_members=:event_members                    
                    WHERE id=:id");
            //Debug::dump($id);
            //exit;
            $parameters = array(
                'id' => $eventsObject->getId(),
                'event_name' => $eventsObject->getEventName(),
                'sponser_name' => $eventsObject->getSponserName(),
                'event_organiser' => $eventsObject->getEventOrganiser(),
                'sponser_photo' => $eventsObject->getSponserPhoto(),
                'organiser_photo' => $eventsObject->getOrganiserPhoto(),
                'organiser_contact' => $eventsObject->getOrganiserContact(),
                'sponser_contact' => $eventsObject->getSponserContact(),
                'is_active' => $eventsObject->getIsActive(),
                'event_desc' => $eventsObject->getEventDesc(),
                'image' => $eventsObject->getImage(),
                'country_id' => $eventsObject->getCountryId(),
                'state_id' => $eventsObject->getStateId(),
                'city_id' => $eventsObject->getCityId(),
                'event_date' => $eventsObject->getEventDate(),
                'end_date' => $eventsObject->getEndDate(),
                'start_time' => $eventsObject->getStartTime(),
                'end_time' => $eventsObject->getEndTime(),
                'venue' => $eventsObject->getVenue(),
                'event_cost' => $eventsObject->getEventCost(),
                'event_members' => $eventsObject->getEventMembers(),
                
                
            );
            $result = $statement->execute($parameters);
            
            if ($result)
                    return "success";
                else
                    return "couldn't update";
        } else {
            
            if(!empty($eventsObject->getSponserPhoto()['name'])){
//                        echo  $eventsObject->getSponserPhoto()['name'];exit;
                $filename = $eventsObject->getSponserPhoto()['name'];
                $fileTmpName = $eventsObject->getSponserPhoto()['tmp_name'];
                $filetype = $eventsObject->getSponserPhoto()['type'];
                $filesize = $eventsObject->getSponserPhoto()['size'];
//                echo  $filesize;exit;
                $filext = pathinfo($filename,PATHINFO_EXTENSION);
//                echo  $filext;exit;
                $imagename = date("d-m-Y")."-".time().$filename;
//                $targetpath = ROOT_PATH.$imagename;
                //echo  $this->basePath();
              //echo  ROOT_PATH.$imagename;
                
                //exit;
                $targetpath = "/var/www/html/rustagi/public/EventsImages/".$imagename;
//                        echo  "<pre>";
//                        print_r($targetpath);exit;
                            //$this->basePath();


                if(in_array($filext, array('jpg','png','gif','JPEG','JPG','jpeg'))){

                        if($filesize<500000)
                    {          //echo  "<pre>";
                        //print_r($filesize);exit;
                        
                       if(move_uploaded_file($fileTmpName, $targetpath))
                        $error = "";                            
                        else $targetpath="";
                    }
                    else  $error = "file size must not be smaller than 5 MB";

                }
                else  $error = "only jpg,png or gif files are allowed";
            }

            else {
                $targetpath="";
            }
            
             $statement = $this->dbAdapter->query("INSERT INTO tbl_upcoming_events 
                 (event_name, sponser_name, event_organiser,sponser_photo, organiser_photo, organiser_contact, sponser_contact, is_active, event_desc, image, country_id, state_id, city_id, event_date, end_date, start_time, end_time, venue, event_cost, event_members, created_date)
                 values(:event_name, :sponser_name, :event_organiser,:sponser_photo, :organiser_photo, :organiser_contact, :sponser_contact, :is_active, :event_desc, :image, :country_id, :state_id, :city_id, :event_date, :end_date, :start_time, :end_time, :venue, :event_cost, :event_members, now())");
                 
           
            $parameters = array(
                'event_name' => $eventsObject->getEventName(),
                'sponser_name' => $eventsObject->getSponserName(),
                'event_organiser' => $eventsObject->getEventOrganiser(),
                'sponser_photo' => $targetpath,
                'organiser_photo' => $eventsObject->getOrganiserPhoto(),
                'organiser_contact' => $eventsObject->getOrganiserContact(),
                'sponser_contact' => $eventsObject->getSponserContact(),
                'is_active' => $eventsObject->getIsActive(),
                'event_desc' => $eventsObject->getEventDesc(),
                'image' => $eventsObject->getImage(),
                'country_id' => $eventsObject->getCountryId(),
                'state_id' => $eventsObject->getStateId(),
                'city_id' => $eventsObject->getCityId(),
                
                'event_date' => date('Y-m-d',strtotime($eventsObject->getEventDate())),                
                'end_date' => date('Y-m-d',strtotime($eventsObject->getEndDate())),
                'start_time' => $eventsObject->getStartTime(),
                'end_time' => $eventsObject->getEndTime(),
                'venue' => $eventsObject->getVenue(),
                
                'event_cost' => $eventsObject->getEventCost(),
                'event_members' => $eventsObject->getEventMembers(),
                
            );
            //print_r($parameters);
            //exit;
            $result = $statement->execute($parameters);
            
            //if ($result) 
           if ($result)
                return "success";
            else
                return "couldn't update";

        //return $respArr;
        }

        if ($result instanceof ResultInterface) {
            if ($newId = $result->getGeneratedValue()) {
                // When a value has been generated, set it on the object
                $eventsObject->setId($newId);
            }

            //print_r($educationFieldsObject);
            //exit;
            
        }
    }
    
    
    public function viewByEventsId($table, $id) {
//            Debug::dump($status);
//        exit;
        //if(isset($status)){
        $statement = $this->dbAdapter->query("SELECT tbl_upcoming_events.*,tbl_country.country_name,tbl_state.state_name, tbl_city.city_name FROM tbl_upcoming_events LEFT JOIN 

tbl_country ON(tbl_upcoming_events.country_id=tbl_country.id)  LEFT JOIN tbl_state

ON(tbl_upcoming_events.state_id=tbl_state.id)  LEFT JOIN tbl_city

ON(tbl_upcoming_events.city_id=tbl_city.id) WHERE tbl_upcoming_events.id=:id");
        
        $parameters = array(
            'id' => $id,
        );
        
        $result = $statement->execute($parameters);
        //}
        // if(isset($status)){
//        Debug::dump($status);
//        exit;
//        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_field  WHERE is_active=:is_active");
//        $parameters = array(
//            'is_active' => $status,
//        );
        //$result = $statement->execute($parameters);
        //$result = $statement->execute();
        //}
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            $resultSet = new HydratingResultSet($this->hydrator, new Events());
//
//            return $resultSet->initialize($result);
//            //return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
        
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new Events());
        }
    }
    
    public function getEvents($id) {
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_upcoming_events WHERE id=:id");
        $parameters = array(
            'id' => $id
        );
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new Events());
        }
    }
    
    
}
