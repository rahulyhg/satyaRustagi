<?php

namespace Admin\Mapper;

use Admin\Model\Entity\EducationFields;
use Admin\Model\Entity\Countries;
use Admin\Model\Entity\States;
use Admin\Model\Entity\Cities;
use Admin\Model\Entity\Religions;
use Admin\Model\Entity\Gothras;
use Admin\Model\Entity\Starsigns;
use Admin\Model\Entity\Zodiacsigns;
use Admin\Model\Entity\Professions;
use Admin\Model\Entity\Designations;
use Admin\Model\Entity\Educationlevels;
use Application\Model\Entity\UserInfo;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Update;
use Zend\Debug\Debug;
use Zend\Stdlib\Hydrator\ClassMethods;

class AdminDbSqlMapper implements AdminMapperInterface {

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
    
    public function getEducationField($id) {
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_field WHERE id=:id");
        $parameters = array(
            'id' => $id
        );
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }

    public function getEducationFieldList($status) {
//            Debug::dump($status);
//        exit;
        //if(isset($status)){
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_field");
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
            $resultSet = new HydratingResultSet($this->hydrator, new EducationFields());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }

    //added by amir
    public function getEducationFieldRadioList($status) {
//            Debug::dump($status);
//        exit;
//        if(empty($status)){
//        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_field");
//        $result = $statement->execute();
//        }
//        if(isset($status)){
//        Debug::dump($status);
//        exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_field  WHERE is_active=:is_active");
        $parameters = array(
            'is_active' => $status,
        );
        $result = $statement->execute($parameters);
        //$result = $statement->execute();
//        }
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new EducationFields());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }

    public function saveEducationField($educationFieldsObject) {
//                print_r($educationFieldsObject);
//                exit;
        $educatioData = $this->hydrator->extract($educationFieldsObject);
        //print_r($educatioData);
        //exit;
        unset($educatioData['id']); // Neither Insert nor Update needs the ID in the array

        if ($educationFieldsObject->getId()) {
//            echo  "<pre>";
//            echo  "hello";exit;
            $statement = $this->dbAdapter->query("UPDATE tbl_education_field 
                SET education_field=:education_field,
                    is_active=:is_active
                    WHERE id=:id");
            //Debug::dump($id);
            //exit;
            $parameters = array(
                'id' => $educationFieldsObject->getId(),
                'education_field' => $educationFieldsObject->getEducationField(),
                'is_active' => $educationFieldsObject->getIsActive(),
            );
            $result = $statement->execute($parameters);
            
            if ($result)
                    return "success";
                else
                    return "couldn't update";
        } else {
             $statement = $this->dbAdapter->query("INSERT INTO tbl_education_field 
                 (education_field, is_active, created_date)
                 values(:education_field, :is_active, now())");
                 
           
            $parameters = array(
                'education_field' => $educationFieldsObject->getEducationField(),
                'is_active' => $educationFieldsObject->getIsActive(),
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
                $educationFieldsObject->setId($newId);
            }

            //print_r($educationFieldsObject);
            //exit;
            
        }
    }

    public function changeStatus($table, $id, $data) {
        $statement = $this->dbAdapter->query("UPDATE $table SET is_active=:is_active WHERE id=:id");
        //Debug::dump($id);
        //exit;
        $parameters = array(
            'id' => $id,
            'is_active' => $data['is_active']
        );
        $result = $statement->execute($parameters);

        if ($result) {
            $respArr = array('status' => "Updated SuccessFully");
        } else {
            $respArr = array('status' => "Couldn't update");
        }

        return $respArr;
    }

    public function changeStatusAll($table, $ids, $data) {
        //print_r(explode(",",$ids));
        //exit;
        $ids = $this->dbAdapter->getPlatform()->quoteValueList(explode(",", $ids));
        //$placeholder=  str_repeat('?, ', count(explode(",",$ids))-1).'?';
        //echo $placeholder;
        //exit;
        $statement = $this->dbAdapter->query("UPDATE $table set is_active=:is_active where id IN ($ids)");
        //print_r($statement);
        //exit;
        $parameters = array(
            //'ids' => $ids,
            'is_active' => $data
        );
        //Debug::dump($statement);
        //exit;
        $result = $statement->execute($parameters);

        if ($result) {
            $respArr = array('status' => "Updated SuccessFully");
        } else {
            $respArr = array('status' => "Couldn't update");
        }

        return $respArr;
    }

    public function delete($table, $id) {
//        echo  "<pre>";
//        print_r($id);exit;
        $statement = $this->dbAdapter->query("DELETE FROM $table where id=:id");
        //Debug::dump($statement);exit;
        $parameters = array(
            'id' => $id
        );
        $result = $statement->execute($parameters);

//        if ($result) {
//            $respArr = array('status' => "Deleted SuccessFully");
//        } else {
//            $respArr = array('status' => "Couldn't deleted");
//        }
//
//        return $respArr;
    }

    public function deleteMultiple($table, $ids) {
//        echo   "<pre>";
//        print_r($ids);exit;
        $ids = $this->dbAdapter->getPlatform()->quoteValueList(explode(",", $ids));
        $statement = $this->dbAdapter->query("DELETE FROM $table where id IN($ids)");

//        $parameters = array(
//            'ids' => $ids,
//        );
        $result = $statement->execute();
    }

    public function viewById($table, $id) {

        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM $table WHERE id=:id");
        //$adapter->query('SELECT * FROM `artist` WHERE `id` = ?', array(5));

        $parameters = array(
            'id' => $id,
        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
        //print_r($result->current());exit;
//        if ($result) {
//            $respArr = array('status' => "Deleted SuccessFully");
//        } else {
//            $respArr = array('status' => "Couldn't deleted");
//        }
//
        //return $result;
    }
    
    
    public function performSearchEducationField($field) {
//        echo   "<pre>";
//        echo  $field;exit;
        $field1 = empty($field) ? "" : "education_field like '" . $field . "%'";
        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_field WHERE " . $field1 . "");
        

//        $parameters = array(
//            'id' => $id,
//        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute();
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
        
         if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new EducationFields());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
       
    }
    
    public function educationFieldSearch($data) {
//        echo   "<pre>";
//        echo  $data;exit;
//        $field1 = empty($field) ? "" : "education_field like '" . $field . "%'";
        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_field WHERE education_field like '" . $data . "%'");
//        \Zend\Debug\Debug::dump($statement);exit;

//        $parameters = array(
//            'id' => $id,
//        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute();
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
        
         if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new EducationFields());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
       
    }
    
    //country sql method
    
    
    public function getCountriesList($status) {
//            Debug::dump($status);
//        exit;
//        echo   "<pre>";echo  "hello3";exit;
        //if(isset($status)){
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_country");
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
            $resultSet = new HydratingResultSet($this->hydrator, new Countries());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
    
    public function getCountryRadioList($status) {
//            Debug::dump($status);
//        exit;
//        if(empty($status)){
//        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_field");
//        $result = $statement->execute();
//        }
//        if(isset($status)){
//        Debug::dump($status);
//        exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_country  WHERE is_active=:is_active");
        $parameters = array(
            'is_active' => $status,
        );
        $result = $statement->execute($parameters);
        //$result = $statement->execute();
//        }
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Countries());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
    
    public function viewByCountryId($table, $id) {

        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM $table WHERE id=:id");
        //$adapter->query('SELECT * FROM `artist` WHERE `id` = ?', array(5));

        $parameters = array(
            'id' => $id,
        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new Countries());
        }
        
    }
    
    public function saveCountry($countryObject) {
//                print_r($educationFieldsObject);
//                exit;
        $countryData = $this->hydrator->extract($countryObject);
        //print_r($educatioData);
        //exit;
        unset($countryData['id']); // Neither Insert nor Update needs the ID in the array

        if ($countryObject->getId()) {
//            echo  "<pre>";
//            echo  "hello";exit;
            $statement = $this->dbAdapter->query("UPDATE tbl_country 
                SET country_name=:country_name,
                    dial_code=:dial_code,
                    country_code=:country_code,
                    master_country_id=:master_country_id
                    WHERE id=:id");
            //Debug::dump($id);
            //exit;
            $parameters = array(
                'id' => $countryObject->getId(),
                'country_name' => $countryObject->getCountryName(),
                'dial_code' => $countryObject->getDialCode(),
                'country_code' => $countryObject->getCountryCode(),
                'master_country_id' => $countryObject->getMasterCountryId()
                //'is_active' => $educationFieldsObject->getIsActive(),
            );
            $result = $statement->execute($parameters);
            
            if ($result)
                    return "success";
                else
                    return "couldn't update";
        } else {
             $statement = $this->dbAdapter->query("INSERT INTO tbl_country 
                 (country_name, is_active, dial_code, country_code, master_country_id, created_date)
                 values(:country_name, 0, :dial_code, :country_code, :master_country_id, now())");
                 
           
            $parameters = array(
                'country_name' => $countryObject->getCountryName(),
                //'is_active' => $countryObject->getIsActive(),
                'dial_code' => $countryObject->getDialCode(),
                'country_code' => $countryObject->getCountryCode(),
                'master_country_id' => $countryObject->getMasterCountryId(),
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
                $educationFieldsObject->setId($newId);
            }

            //print_r($educationFieldsObject);
            //exit;
            
        }
    }
    
    
    public function getCountry($id) {
        //echo   "<pre>";echo  $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_country WHERE id=:id");
        $parameters = array(
            'id' => $id
        );
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new Countries());
        }
    }
    
    
    public function performSearchCountry($field,$field2,$field3) {
//        echo   "<pre>";
//        echo  $field3;exit;
//        $field1 = empty($field) ? "" : "education_field like '" . $field . "%'";
        //echo $id;exit;
        $field1 = empty($field) ? "" : "country_name like '" . $field . "%' &&";
        $field4 = empty($field2) ? "" : "country_code like '" . $field2 . "%' &&";
        $field5 = empty($field3) ? "" : "dial_code like '" . $field3 . "%' ";
        
        $sql = "select * from tbl_country where " . $field1 . $field4 . $field5 . "";
        $sql = rtrim($sql, "&&");
        
        $statement = $this->dbAdapter->query($sql);
        

//        $parameters = array(
//            'id' => $id,
//        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute();
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
        
         if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Countries());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
       
    }
    
    public function countrySearch($data) {
//        echo   "<pre>";
//        echo  $data;exit;
//        $field1 = empty($field) ? "" : "education_field like '" . $field . "%'";
        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_country WHERE country_name like '" . $data . "%'");
//        \Zend\Debug\Debug::dump($statement);exit;

//        $parameters = array(
//            'id' => $id,
//        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute();
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
        
         if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Countries());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
       
    }
    
    //state sql
    
    public function getStatesList() {
//            Debug::dump($status);
//        exit;
        //if(isset($status)){
        $statement = $this->dbAdapter->query("SELECT tbl_state.*, tbl_country.country_name AS country_name FROM tbl_state 
                INNER JOIN tbl_country ON tbl_state.country_id = tbl_country.id");
        
                    
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
            $resultSet = new HydratingResultSet($this->hydrator, new States());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
    public function customFields() {
        //echo   "<pre>";echo  "hello";exit;
//        $countryName = $this->tableGateway->select(function(Select $select) use($columns) {
//                    $select->order('id ASC');
//                    $select->columns($columns);
//                })->toArray();
//
//        foreach ($countryName as $list) {
//            $countrynamelist[$list['id']] = $list['country_name'];
//        }
        // print_r($countrynamelist);die;
//        return $countrynamelist;
        //
        $statement = $this->dbAdapter->query("SELECT id,country_name FROM tbl_country WHERE 1");
        
        $result = $statement->execute();
        
        foreach ($result as $list) {
            $countrynamelist[$list['id']] = $list['country_name'];
        }
        // print_r($countrynamelist);die;
        return $countrynamelist;
        
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            $resultSet = new HydratingResultSet($this->hydrator, new States());
//
//            return $resultSet->initialize($result);
//            //return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
    }
    
    
    public function performSearchState($field,$field2) {
//        echo   "<pre>";
//        echo  $field2;exit;
//        $field1 = empty($field) ? "" : "education_field like '" . $field . "%'";
        //echo $id;exit;
//        $field1 = empty($field) ? "" : "country_name like '" . $field . "%' &&";
//        $field4 = empty($field2) ? "" : "country_code like '" . $field2 . "%' &&";
//        $field5 = empty($field3) ? "" : "dial_code like '" . $field3 . "%' ";
        $field3 = empty($field)? "": "tbl_state.country_id= '".$field."' &&";   
        $field4 = empty($field2)? "": " tbl_state.state_name like '".$field2."%' "; 
        
//        $sql = "select * from tbl_country where " . $field1 . $field4 . $field5 . "";
        
        $sql = "select tbl_state.*,tbl_country.country_name AS country_name from tbl_state inner join 
             tbl_country on tbl_state.country_id = tbl_country.id 
         where ".$field3.$field4."";
        $sql = rtrim($sql, "&&");
//        echo   "<pre>";
//                print_r($sql);exit;
        $statement = $this->dbAdapter->query($sql);
        

//        $parameters = array(
//            'id' => $id,
//        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute();
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
        
         if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new States());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
       
    }
    
    
    public function saveState($stateObject) {
//                print_r($educationFieldsObject);
//                exit;
        $stateData = $this->hydrator->extract($stateObject);
        //print_r($educatioData);
        //exit;
        unset($stateData['id']); // Neither Insert nor Update needs the ID in the array

        if ($stateObject->getId()) {
//            echo  "<pre>";
//            echo  "hello";exit;
            $statement = $this->dbAdapter->query("UPDATE tbl_state 
                SET state_name=:state_name,
                    is_active=:is_active,
                    country_id=:country_id                    
                    WHERE id=:id");
            //Debug::dump($id);
            //exit;
            $parameters = array(
                'id' => $stateObject->getId(),
                'state_name' => $stateObject->getStateName(),
                'is_active' => $stateObject->getIsActive(),
                'country_id' => $stateObject->getCountryId()
                //'master_country_id' => $countryObject->getMasterCountryId()
                //'is_active' => $educationFieldsObject->getIsActive(),
            );
            $result = $statement->execute($parameters);
            
            if ($result)
                    return "success";
                else
                    return "couldn't update";
        } else {
             $statement = $this->dbAdapter->query("INSERT INTO tbl_state 
                 (state_name, is_active,country_id, created_date)
                 values(:state_name, :is_active,:country_id, now())");
                 
           
            $parameters = array(
                'state_name' => $stateObject->getStateName(),
                //'is_active' => $countryObject->getIsActive(),
                'is_active' => $stateObject->getIsActive(),
                'country_id' => $stateObject->getCountryId()
                //'master_country_id' => $countryObject->getMasterCountryId(),
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
                $stateObject->setId($newId);
            }

            //print_r($educationFieldsObject);
            //exit;
            
        }
    }
    
    
    public function getState($id) {
        //echo   "<pre>";echo  $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_state WHERE id=:id");
        $parameters = array(
            'id' => $id
        );
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new States());
        }
    }
    
    public function getStateRadioList($status) {
//            Debug::dump($status);
//        exit;
//        if(empty($status)){
//        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_field");
//        $result = $statement->execute();
//        }
//        if(isset($status)){
//        Debug::dump($status);
//        exit;
        $statement = $this->dbAdapter->query("SELECT tbl_state.*,tbl_country.country_name FROM tbl_state INNER JOIN tbl_country ON(tbl_country.id=tbl_state.country_id) WHERE tbl_state.is_active=:is_active");
        $parameters = array(
            'is_active' => $status,
        );
        $result = $statement->execute($parameters);
        //$result = $statement->execute();
//        }
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new States());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
    
    public function viewByStateId($table, $id) {

        //echo $id;exit;
//        $statement = $this->dbAdapter->query("SELECT * FROM $table WHERE id=:id");
        $statement = $this->dbAdapter->query("SELECT tbl_state.*,tbl_country.country_name FROM tbl_state INNER JOIN tbl_country ON(tbl_country.id=tbl_state.country_id) WHERE tbl_state.id=:id");
        
        //$adapter->query('SELECT * FROM `artist` WHERE `id` = ?', array(5));

        $parameters = array(
            'id' => $id,
        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new States());
        }
        
    }
    
    
    public function stateSearch($data,$field) {
//        echo   "<pre>";
//        echo  $data;exit;
//        $field1 = empty($field) ? "" : "education_field like '" . $field . "%'";
        //echo $id;exit;
        
        $countryname = (empty($fieldname = $field))?"":" && tbl_state.country_id=".$field;
//        \Zend\Debug\Debug::dump($statement);exit;
        //$statement = $this->dbAdapter->query("SELECT * FROM tbl_religion WHERE religion_name like '" . $data . "%'");
        $statement = $this->dbAdapter->query("select * from tbl_state inner join tbl_country on tbl_state.country_id = tbl_country.id
            where (tbl_state.state_name like '$data%' ".$countryname.")");

//        $parameters = array(
//            'id' => $id,
//        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute();
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
        
         if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new States());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
       
    }
    
    //city
    
    public function getCitiesList() {
//            Debug::dump($status);
//        exit;
        //if(isset($status)){
        $statement = $this->dbAdapter->query("SELECT tbl_city.*, tbl_state.state_name AS state_name FROM tbl_city 
                INNER JOIN tbl_state ON tbl_city.state_id = tbl_state.id");
        
                    
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
            $resultSet = new HydratingResultSet($this->hydrator, new Cities());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
    
//    public function getCountriesList() {
////            Debug::dump($status);
////        exit;
//        //if(isset($status)){
//        $statement = $this->dbAdapter->query("SELECT tbl_city.*, tbl_state.state_name AS state_name FROM tbl_city 
//                INNER JOIN tbl_state ON tbl_city.state_id = tbl_state.id");
//        
//                    
//        $result = $statement->execute();
//        //}
//        // if(isset($status)){
////        Debug::dump($status);
////        exit;
////        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_field  WHERE is_active=:is_active");
////        $parameters = array(
////            'is_active' => $status,
////        );
//        //$result = $statement->execute($parameters);
//        //$result = $statement->execute();
//        //}
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            $resultSet = new HydratingResultSet($this->hydrator, new Cities());
//
//            return $resultSet->initialize($result);
//            //return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
//    }
    
    
    public function getStateListByCountryCode($Country_ID) {
//            echo  "<pre>";
//            print_r($Country_ID);
//            exit;
        //if(isset($status)){
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_state 
                WHERE country_id = :country_id");
        $parameters = array(
            'country_id' => $Country_ID,
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
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new States());

            return $resultSet->initialize($result)->toArray();
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
    
    public function getCityListByStateCode($State_ID) {
//            echo  "<pre>";
//            print_r($Country_ID);
//            exit;
        //if(isset($status)){
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_city 
                WHERE state_id = :state_id");
        $parameters = array(
            'state_id' => $State_ID,
        );
                    
        $result = $statement->execute($parameters);
        
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Cities());

            return $resultSet->initialize($result)->toArray();
           
        }
    }
    
    public function getCityListByCountry($country_id) {
//            echo  "<pre>";
//            print_r($country_id);
//            exit;
        //if(isset($status)){
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_state WHERE country_id=:country_id");
        $parameters = array(
            'country_id' => $country_id,
        );
                    //$sql = "SELECT * FROM tbl_state WHERE country_id=".$_POST['Country_id']."";
        $results = $statement->execute($parameters);
        
        if ($results instanceof ResultInterface && $results->isQueryResult() && $results->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new States());

             $resultSet->initialize($results);
           
        }
//        echo  "<pre>";
//        print_r($results);exit;
        $i=0;
            $data = array();
            foreach($results as $result){
               
               $data[$i] = $result['id'];
               
                $i++;
            }
            
             $states_id = implode(',',$data);
//             print_r($states_id);exit;
             
             $statement2 = $this->dbAdapter->query("SELECT tbl_city.*,tbl_state.state_name FROM tbl_city INNER JOIN tbl_state ON 
                    tbl_city.state_id=tbl_state.id where tbl_city.state_id IN($states_id)");
             $results1 = $statement2->execute();
             
             if ($results1 instanceof ResultInterface && $results1->isQueryResult() && $results1->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Cities());

          return   $resultSet->initialize($results1);
           
        }
             
             
    }
    
    public function getCityListByState($state_id) {
//            echo  "<pre>";
//            print_r($Country_ID);
//            exit;
        //if(isset($status)){
        $statement = $this->dbAdapter->query("SELECT tbl_city.*,tbl_state.state_name FROM tbl_city INNER JOIN tbl_state ON 
                    tbl_city.state_id=tbl_state.id where tbl_city.state_id=:state_id");
        $parameters = array(
            'state_id' => $state_id,
        );
                    
        $result = $statement->execute($parameters);
        
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Cities());

            return $resultSet->initialize($result);
           
        }
    }
    
    public function getCityListByCity($city_id) {
//            echo  "<pre>";
//            print_r($Country_ID);
//            exit;
        //if(isset($status)){
        $statement = $this->dbAdapter->query("SELECT tbl_city.*,tbl_state.state_name FROM tbl_city INNER JOIN tbl_state ON 
                    tbl_city.state_id=tbl_state.id where tbl_city.id=:city_id");
        $parameters = array(
            'city_id' => $city_id,
        );
                    
        $result = $statement->execute($parameters);
        
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Cities());

            return $resultSet->initialize($result);
           
        }
    }
    
    public function customFieldsState() {        
        
        $statement = $this->dbAdapter->query("SELECT id,state_name FROM tbl_state WHERE 1");
        
        $result = $statement->execute();
        
        foreach ($result as $list) {
            $statenamelist[$list['id']] = $list['state_name'];
        }
        
        return $statenamelist;        
   
    }
    
    public function SaveCity($cityObject) {
//                print_r($educationFieldsObject);
//                exit;
        $cityData = $this->hydrator->extract($cityObject);
        //print_r($educatioData);
        //exit;
        unset($cityData['id']); // Neither Insert nor Update needs the ID in the array

        if ($cityObject->getId()) {
//            echo  "<pre>";
//            echo  "hello";exit;
            $statement = $this->dbAdapter->query("UPDATE tbl_city 
                SET city_name=:city_name,
                    state_id=:state_id
                    WHERE id=:id");
            //Debug::dump($id);
            //exit;
            $parameters = array(
                'id' => $cityObject->getId(),
                'city_name' => $cityObject->getCityName(),
                'state_id' => $cityObject->getStateId(),
            );
            $result = $statement->execute($parameters);
            
            if ($result)
                    return "success";
                else
                    return "couldn't update";
        } else {
             $statement = $this->dbAdapter->query("INSERT INTO tbl_city 
                 (city_name, state_id, created_date)
                 values(:city_name, :state_id, now())");
                 
           
            $parameters = array(
                'city_name' => $cityObject->getCityName(),
                'state_id' => $cityObject->getStateId()
                //'is_active' => $cityObject->getIsActive(),
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
                $cityObject->setId($newId);
            }

            //print_r($educationFieldsObject);
            //exit;
            
        }
    }
    
    public function getCity($id) {
        //echo   "<pre>";echo  $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_city WHERE id=:id");
        $parameters = array(
            'id' => $id
        );
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new Cities());
        }
    }
    
    
    public function getCityRadioList($status) {
//            Debug::dump($status);
//        exit;
//        if(empty($status)){
//        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_field");
//        $result = $statement->execute();
//        }
//        if(isset($status)){
//        Debug::dump($status);
//        exit;
        $statement = $this->dbAdapter->query("SELECT tbl_city.*,tbl_state.state_name FROM tbl_city INNER JOIN tbl_state ON(tbl_state.id=tbl_city.state_id) WHERE tbl_city.is_active=:is_active");
        $parameters = array(
            'is_active' => $status,
        );
        $result = $statement->execute($parameters);
        //$result = $statement->execute();
//        }
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Cities());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
    public function viewByCityId($table, $id) {

        //echo $id;exit;
//        $statement = $this->dbAdapter->query("SELECT * FROM $table WHERE id=:id");
        $statement = $this->dbAdapter->query("SELECT tbl_city.*,tbl_state.state_name FROM tbl_city INNER JOIN tbl_state ON(tbl_state.id=tbl_city.state_id) WHERE tbl_city.id=:id");
        
        //$adapter->query('SELECT * FROM `artist` WHERE `id` = ?', array(5));

        $parameters = array(
            'id' => $id,
        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new Cities());
        }
        
    }
    
    //Religion
    
   
    public function getReligionList($status) {
//            Debug::dump($status);
//        exit;
        //if(isset($status)){
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_religion");
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
            $resultSet = new HydratingResultSet($this->hydrator, new Religions());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
    public function SaveReligion($religionObject) {
//                print_r($educationFieldsObject);
//                exit;
        $religionData = $this->hydrator->extract($religionObject);
        //print_r($educatioData);
        //exit;
        unset($religionData['id']); // Neither Insert nor Update needs the ID in the array

        if ($religionObject->getId()) {
//            echo  "<pre>";
//            echo  "hello";exit;
            $statement = $this->dbAdapter->query("UPDATE tbl_religion 
                SET religion_name=:religion_name,
                    is_active=:is_active
                    WHERE id=:id");
            //Debug::dump($id);
            //exit;
            $parameters = array(
                'id' => $religionObject->getId(),
                'religion_name' => $religionObject->getReligionName(),
                'is_active' => $religionObject->getIsActive(),
            );
            $result = $statement->execute($parameters);
            
            if ($result)
                    return "success";
                else
                    return "couldn't update";
        } else {
             $statement = $this->dbAdapter->query("INSERT INTO tbl_religion 
                 (religion_name, is_active, created_date)
                 values(:religion_name, :is_active, now())");
                 
           
            $parameters = array(
                'religion_name' => $religionObject->getReligionName(),
                'is_active' => $religionObject->getIsActive(),
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
                $religionObject->setId($newId);
            }

            //print_r($educationFieldsObject);
            //exit;
            
        }
    }
    
     public function getReligion($id) {
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_religion WHERE id=:id");
        $parameters = array(
            'id' => $id
        );
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new Religions());
        }
    }
    
    public function religionSearch($data) {
//        echo   "<pre>";
//        echo  $data;exit;
//        $field1 = empty($field) ? "" : "education_field like '" . $field . "%'";
        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_religion WHERE religion_name like '" . $data . "%'");
//        \Zend\Debug\Debug::dump($statement);exit;

//        $parameters = array(
//            'id' => $id,
//        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute();
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
        
         if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Religions());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
       
    }
    
    public function performSearchReligion($field) {
//        echo   "<pre>";
//        echo  $field;exit;
        $field1 = empty($field) ? "" : "religion_name like '" . $field . "%'";
        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_religion WHERE " . $field1 . "");
        

//        $parameters = array(
//            'id' => $id,
//        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute();
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
        
         if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Religions());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
       
    }
    
    public function getReligionRadioList($status) {
//            Debug::dump($status);
//        exit;
//        if(empty($status)){
//        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_field");
//        $result = $statement->execute();
//        }
//        if(isset($status)){
//        Debug::dump($status);
//        exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_religion  WHERE is_active=:is_active");
        $parameters = array(
            'is_active' => $status,
        );
        $result = $statement->execute($parameters);
        //$result = $statement->execute();
//        }
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Religions());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
    public function viewByReligionId($table, $id) {

        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM $table WHERE id=:id");
        //$adapter->query('SELECT * FROM `artist` WHERE `id` = ?', array(5));

        $parameters = array(
            'id' => $id,
        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new Religions());
        }
        //print_r($result->current());exit;
//        if ($result) {
//            $respArr = array('status' => "Deleted SuccessFully");getReligionList
//        } else {
//            $respArr = array('status' => "Couldn't deleted");
//        }
//
        //return $result;
    }
    
    //Gothras
    
    public function getGothrasList($status) {
//            Debug::dump($status);
//        exit;
        //if(isset($status)){
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_gothra_gothram");
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
            $resultSet = new HydratingResultSet($this->hydrator, new Gothras());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
    public function SaveGothra($gothraObject) {
//                print_r($educationFieldsObject);
//                exit;
        $gothraData = $this->hydrator->extract($gothraObject);
        //print_r($educatioData);
        //exit;
        unset($gothraData['id']); // Neither Insert nor Update needs the ID in the array

        if ($gothraObject->getId()) {
//            echo  "<pre>";
//            echo  "hello";exit;
            $statement = $this->dbAdapter->query("UPDATE tbl_gothra_gothram 
                SET gothra_name=:gothra_name,
                    is_active=:is_active
                    WHERE id=:id");
            //Debug::dump($id);
            //exit;
            $parameters = array(
                'id' => $gothraObject->getId(),
                'gothra_name' => $gothraObject->getGothraName(),
                'is_active' => $gothraObject->getIsActive(),
            );
            $result = $statement->execute($parameters);
            
            if ($result)
                    return "success";
                else
                    return "couldn't update";
        } else {
             $statement = $this->dbAdapter->query("INSERT INTO tbl_gothra_gothram 
                 (gothra_name, is_active, created_date)
                 values(:gothra_name, :is_active, now())");
                 
           
            $parameters = array(
                'gothra_name' => $gothraObject->getGothraName(),
                'is_active' => $gothraObject->getIsActive(),
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
                $gothraObject->setId($newId);
            }

            //print_r($educationFieldsObject);
            //exit;
            
        }
    }
    
    public function getGothra($id) {
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_gothra_gothram WHERE id=:id");
        $parameters = array(
            'id' => $id
        );
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new Gothras());
        }
    }
    
    
    public function gothraSearch($data) {
//        echo   "<pre>";
//        echo  $data;exit;
//        $field1 = empty($field) ? "" : "education_field like '" . $field . "%'";
        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_gothra_gothram WHERE gothra_name like '" . $data . "%'");
//        \Zend\Debug\Debug::dump($statement);exit;

//        $parameters = array(
//            'id' => $id,
//        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute();
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
        
         if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Gothras());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
       
    }
    
    public function performSearchGothra($field) {
//        echo   "<pre>";
//        echo  $field;exit;
        $field1 = empty($field) ? "" : "gothra_name like '" . $field . "%'";
        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_gothra_gothram WHERE " . $field1 . "");
        

//        $parameters = array(
//            'id' => $id,
//        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute();
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
        
         if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Gothras());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
       
    }
    
    public function getGothraRadioList($status) {
//            Debug::dump($status);
//        exit;
//        if(empty($status)){
//        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_field");
//        $result = $statement->execute();
//        }
//        if(isset($status)){
//        Debug::dump($status);
//        exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_gothra_gothram  WHERE is_active=:is_active");
        $parameters = array(
            'is_active' => $status,
        );
        $result = $statement->execute($parameters);
        //$result = $statement->execute();
//        }
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Gothras());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
    public function viewByGothraId($table, $id) {

        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM $table WHERE id=:id");
        //$adapter->query('SELECT * FROM `artist` WHERE `id` = ?', array(5));

        $parameters = array(
            'id' => $id,
        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new Gothras());
        }
        //print_r($result->current());exit;
//        if ($result) {
//            $respArr = array('status' => "Deleted SuccessFully");getReligionList
//        } else {
//            $respArr = array('status' => "Couldn't deleted");
//        }
//
        //return $result;
    }
    
    //Starsign
    
    public function getStarsignList($status) {
//            Debug::dump($status);
//        exit;
        //if(isset($status)){
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_star_sign");
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
            $resultSet = new HydratingResultSet($this->hydrator, new Starsigns());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
    public function SaveStarsign($starsignObject) {
//                print_r($educationFieldsObject);
//                exit;
        $starsignData = $this->hydrator->extract($starsignObject);
        //print_r($educatioData);
        //exit;
        unset($starsignData['id']); // Neither Insert nor Update needs the ID in the array

        if ($starsignObject->getId()) {
//            echo  "<pre>";
//            echo  "hello";exit;
            $statement = $this->dbAdapter->query("UPDATE tbl_star_sign 
                SET star_sign_name=:star_sign_name,
                    is_active=:is_active
                    WHERE id=:id");
            //Debug::dump($id);
            //exit;
            $parameters = array(
                'id' => $starsignObject->getId(),
                'star_sign_name' => $starsignObject->getStarSignName(),
                'is_active' => $starsignObject->getIsActive(),
            );
            $result = $statement->execute($parameters);
            
            if ($result)
                    return "success";
                else
                    return "couldn't update";
        } else {
             $statement = $this->dbAdapter->query("INSERT INTO tbl_star_sign 
                 (star_sign_name, is_active, created_date)
                 values(:star_sign_name, :is_active, now())");
                 
           
            $parameters = array(
                'star_sign_name' => $starsignObject->getStarSignName(),
                'is_active' => $starsignObject->getIsActive(),
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
                $starsignObject->setId($newId);
            }

            //print_r($educationFieldsObject);
            //exit;
            
        }
    }
    
    public function getStarsign($id) {
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_star_sign WHERE id=:id");
        $parameters = array(
            'id' => $id
        );
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new Starsigns());
        }
    }
    
    public function starsignSearch($data) {
//        echo   "<pre>";
//        echo  $data;exit;
//        $field1 = empty($field) ? "" : "education_field like '" . $field . "%'";
        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_star_sign WHERE star_sign_name like '" . $data . "%'");
//        \Zend\Debug\Debug::dump($statement);exit;

//        $parameters = array(
//            'id' => $id,
//        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute();
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
        
         if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Starsigns());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
       
    }
    
    public function performSearchStarsign($field) {
//        echo   "<pre>";
//        echo  $field;exit;
        $field1 = empty($field) ? "" : "star_sign_name like '" . $field . "%'";
        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_star_sign WHERE " . $field1 . "");
        

//        $parameters = array(
//            'id' => $id,
//        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute();
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
        
         if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Starsigns());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
       
    }
    
    public function getStarsignRadioList($status) {
//            Debug::dump($status);
//        exit;
//        if(empty($status)){
//        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_field");
//        $result = $statement->execute();
//        }
//        if(isset($status)){
//        Debug::dump($status);
//        exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_star_sign  WHERE is_active=:is_active");
        $parameters = array(
            'is_active' => $status,
        );
        $result = $statement->execute($parameters);
        //$result = $statement->execute();
//        }
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Starsigns());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
    public function viewByStarsignId($table, $id) {

        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM $table WHERE id=:id");
        //$adapter->query('SELECT * FROM `artist` WHERE `id` = ?', array(5));

        $parameters = array(
            'id' => $id,
        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new Starsigns());
        }
        //print_r($result->current());exit;
//        if ($result) {
//            $respArr = array('status' => "Deleted SuccessFully");getReligionList
//        } else {
//            $respArr = array('status' => "Couldn't deleted");
//        }
//
        //return $result;
    }
    
    //Zodiacsign
    
    public function getZodiacsignList($status) {
//            Debug::dump($status);
//        exit;
        //if(isset($status)){
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_zodiac_sign_raasi");
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
            $resultSet = new HydratingResultSet($this->hydrator, new Zodiacsigns());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
    public function SaveZodiacsign($zodiacsignObject) {
//                print_r($educationFieldsObject);
//                exit;
        $zodiacsignData = $this->hydrator->extract($zodiacsignObject);
        //print_r($educatioData);
        //exit;
        unset($zodiacsignData['id']); // Neither Insert nor Update needs the ID in the array

        if ($zodiacsignObject->getId()) {
//            echo  "<pre>";
//            echo  "hello";exit;
            $statement = $this->dbAdapter->query("UPDATE tbl_zodiac_sign_raasi 
                SET zodiac_sign_name=:zodiac_sign_name,
                    is_active=:is_active
                    WHERE id=:id");
            //Debug::dump($id);
            //exit;
            $parameters = array(
                'id' => $zodiacsignObject->getId(),
                'zodiac_sign_name' => $zodiacsignObject->getZodiacSignName(),
                'is_active' => $zodiacsignObject->getIsActive(),
            );
            $result = $statement->execute($parameters);
            
            if ($result)
                    return "success";
                else
                    return "couldn't update";
        } else {
             $statement = $this->dbAdapter->query("INSERT INTO tbl_zodiac_sign_raasi 
                 (zodiac_sign_name, is_active, created_date)
                 values(:zodiac_sign_name, :is_active, now())");
                 
           
            $parameters = array(
                'zodiac_sign_name' => $zodiacsignObject->getZodiacSignName(),
                'is_active' => $zodiacsignObject->getIsActive(),
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
                $zodiacsignObject->setId($newId);
            }

            //print_r($educationFieldsObject);
            //exit;
            
        }
    }
    
    public function getZodiacsign($id) {
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_zodiac_sign_raasi WHERE id=:id");
        $parameters = array(
            'id' => $id
        );
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new Zodiacsigns());
        }
    }
    
    public function zodiacsignSearch($data) {
//        echo   "<pre>";
//        echo  $data;exit;
//        $field1 = empty($field) ? "" : "education_field like '" . $field . "%'";
        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_zodiac_sign_raasi WHERE zodiac_sign_name like '" . $data . "%'");
//        \Zend\Debug\Debug::dump($statement);exit;

//        $parameters = array(
//            'id' => $id,
//        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute();
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
        
         if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Zodiacsigns());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
       
    }
    
    public function performSearchZodiacsign($field) {
//        echo   "<pre>";
//        echo  $field;exit;
        $field1 = empty($field) ? "" : "zodiac_sign_name like '" . $field . "%'";
        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_zodiac_sign_raasi WHERE " . $field1 . "");
        

//        $parameters = array(
//            'id' => $id,
//        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute();
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
        
         if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Zodiacsigns());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
       
    }
    
    public function getZodiacsignRadioList($status) {
//            Debug::dump($status);
//        exit;
//        if(empty($status)){
//        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_field");
//        $result = $statement->execute();
//        }
//        if(isset($status)){
//        Debug::dump($status);
//        exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_zodiac_sign_raasi  WHERE is_active=:is_active");
        $parameters = array(
            'is_active' => $status,
        );
        $result = $statement->execute($parameters);
        //$result = $statement->execute();
//        }
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Zodiacsigns());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
    public function viewByZodiacsignId($table, $id) {

        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM $table WHERE id=:id");
        //$adapter->query('SELECT * FROM `artist` WHERE `id` = ?', array(5));

        $parameters = array(
            'id' => $id,
        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new Zodiacsigns());
        }
        //print_r($result->current());exit;
//        if ($result) {
//            $respArr = array('status' => "Deleted SuccessFully");getReligionList
//        } else {
//            $respArr = array('status' => "Couldn't deleted");
//        }
//
        //return $result;
    }
    
    //Profession
    
    public function getProfessionList($status) {
//            Debug::dump($status);
//        exit;
        //if(isset($status)){
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_profession");
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
            $resultSet = new HydratingResultSet($this->hydrator, new Professions());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
    public function SaveProfession($professionObject) {
//                print_r($educationFieldsObject);
//                exit;
        $professionData = $this->hydrator->extract($professionObject);
        //print_r($educatioData);
        //exit;
        unset($professionData['id']); // Neither Insert nor Update needs the ID in the array

        if ($professionObject->getId()) {
//            echo  "<pre>";
//            echo  "hello";exit;
            $statement = $this->dbAdapter->query("UPDATE tbl_profession 
                SET profession=:profession,
                    is_active=:is_active
                    WHERE id=:id");
            //Debug::dump($id);
            //exit;
            $parameters = array(
                'id' => $professionObject->getId(),
                'profession' => $professionObject->getProfession(),
                'is_active' => $professionObject->getIsActive(),
            );
            $result = $statement->execute($parameters);
            
            if ($result)
                    return "success";
                else
                    return "couldn't update";
        } else {
             $statement = $this->dbAdapter->query("INSERT INTO tbl_profession 
                 (profession, is_active, created_date)
                 values(:profession, :is_active, now())");
                 
           
            $parameters = array(
                'profession' => $professionObject->getProfession(),
                'is_active' => $professionObject->getIsActive(),
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
                $professionObject->setId($newId);
            }

            //print_r($educationFieldsObject);
            //exit;
            
        }
    }
    
    public function getProfession($id) {
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_profession WHERE id=:id");
        $parameters = array(
            'id' => $id
        );
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new Professions());
        }
    }
    
    
    public function professionSearch($data) {
//        echo   "<pre>";
//        echo  $data;exit;
//        $field1 = empty($field) ? "" : "education_field like '" . $field . "%'";
        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_profession WHERE profession like '" . $data . "%'");
//        \Zend\Debug\Debug::dump($statement);exit;

//        $parameters = array(
//            'id' => $id,
//        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute();
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
        
         if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Professions());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
       
    }
    
    public function performSearchProfession($field) {
//        echo   "<pre>";
//        echo  $field;exit;
        $field1 = empty($field) ? "" : "profession like '" . $field . "%'";
        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_profession WHERE " . $field1 . "");
        

//        $parameters = array(
//            'id' => $id,
//        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute();
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
        
         if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Professions());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
       
    }
    
    public function getProfessionRadioList($status) {
//            Debug::dump($status);
//        exit;
//        if(empty($status)){
//        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_field");
//        $result = $statement->execute();
//        }
//        if(isset($status)){
//        Debug::dump($status);
//        exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_profession  WHERE is_active=:is_active");
        $parameters = array(
            'is_active' => $status,
        );
        $result = $statement->execute($parameters);
        //$result = $statement->execute();
//        }
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Professions());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
     public function viewByProfessionId($table, $id) {

        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM $table WHERE id=:id");
        //$adapter->query('SELECT * FROM `artist` WHERE `id` = ?', array(5));

        $parameters = array(
            'id' => $id,
        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new Professions());
        }
        //print_r($result->current());exit;
//        if ($result) {
//            $respArr = array('status' => "Deleted SuccessFully");getReligionList
//        } else {
//            $respArr = array('status' => "Couldn't deleted");
//        }
//
        //return $result;
    }
    
    //Designation
    
    public function getDesignationList($status) {
//            Debug::dump($status);
//        exit;
        //if(isset($status)){
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_designation");
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
            $resultSet = new HydratingResultSet($this->hydrator, new Designations());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
    public function SaveDesignation($designationObject) {
//                print_r($educationFieldsObject);
//                exit;
        $designationData = $this->hydrator->extract($designationObject);
        //print_r($educatioData);
        //exit;
        unset($designationData['id']); // Neither Insert nor Update needs the ID in the array

        if ($designationObject->getId()) {
//            echo  "<pre>";
//            echo  "hello";exit;
            $statement = $this->dbAdapter->query("UPDATE tbl_designation 
                SET designation=:designation,
                    is_active=:is_active
                    WHERE id=:id");
            //Debug::dump($id);
            //exit;
            $parameters = array(
                'id' => $designationObject->getId(),
                'designation' => $designationObject->getDesignation(),
                'is_active' => $designationObject->getIsActive(),
            );
            $result = $statement->execute($parameters);
            
            if ($result)
                    return "success";
                else
                    return "couldn't update";
        } else {
             $statement = $this->dbAdapter->query("INSERT INTO tbl_designation 
                 (designation, is_active, created_date)
                 values(:designation, :is_active, now())");
                 
           
            $parameters = array(
                'designation' => $designationObject->getDesignation(),
                'is_active' => $designationObject->getIsActive(),
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
                $designationObject->setId($newId);
            }

            //print_r($educationFieldsObject);
            //exit;
            
        }
    }
    
    public function getDesignation($id) {
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_designation WHERE id=:id");
        $parameters = array(
            'id' => $id
        );
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new Designations());
        }
    }
    
    public function designationSearch($data) {
//        echo   "<pre>";
//        echo  $data;exit;
//        $field1 = empty($field) ? "" : "education_field like '" . $field . "%'";
        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_designation WHERE designation like '" . $data . "%'");
//        \Zend\Debug\Debug::dump($statement);exit;

//        $parameters = array(
//            'id' => $id,
//        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute();
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
        
         if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Designations());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
       
    }
    
    public function performSearchDesignation($field) {
//        echo   "<pre>";
//        echo  $field;exit;
        $field1 = empty($field) ? "" : "designation like '" . $field . "%'";
        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_designation WHERE " . $field1 . "");
        

//        $parameters = array(
//            'id' => $id,
//        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute();
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
        
         if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Designations());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
       
    }
    
    public function getDesignationRadioList($status) {
//            Debug::dump($status);
//        exit;
//        if(empty($status)){
//        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_field");
//        $result = $statement->execute();
//        }
//        if(isset($status)){
//        Debug::dump($status);
//        exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_designation  WHERE is_active=:is_active");
        $parameters = array(
            'is_active' => $status,
        );
        $result = $statement->execute($parameters);
        //$result = $statement->execute();
//        }
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Designations());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
    public function viewByDesignationId($table, $id) {

        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM $table WHERE id=:id");
        //$adapter->query('SELECT * FROM `artist` WHERE `id` = ?', array(5));

        $parameters = array(
            'id' => $id,
        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new Designations());
        }
        //print_r($result->current());exit;
//        if ($result) {
//            $respArr = array('status' => "Deleted SuccessFully");getReligionList
//        } else {
//            $respArr = array('status' => "Couldn't deleted");
//        }
//
        //return $result;
    }
    
    //Education level
    
    public function getEducationlevelList($status) {
//            Debug::dump($status);
//        exit;
        //if(isset($status)){
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_level");
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
            $resultSet = new HydratingResultSet($this->hydrator, new Educationlevels());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
    
    public function SaveEducationlevel($educationlevelObject) {
//                print_r($educationFieldsObject);
//                exit;
        $educationlevelData = $this->hydrator->extract($educationlevelObject);
        //print_r($educatioData);
        //exit;
        unset($educationlevelData['id']); // Neither Insert nor Update needs the ID in the array

        if ($educationlevelObject->getId()) {
//            echo  "<pre>";
//            echo  "hello";exit;
            $statement = $this->dbAdapter->query("UPDATE tbl_education_level 
                SET education_level=:education_level,
                    is_active=:is_active
                    WHERE id=:id");
            //Debug::dump($id);
            //exit;
            $parameters = array(
                'id' => $educationlevelObject->getId(),
                'education_level' => $educationlevelObject->getEducationLevel(),
                'is_active' => $educationlevelObject->getIsActive(),
            );
            $result = $statement->execute($parameters);
            
            if ($result)
                    return "success";
                else
                    return "couldn't update";
        } else {
             $statement = $this->dbAdapter->query("INSERT INTO tbl_education_level 
                 (education_level, is_active, created_date)
                 values(:education_level, :is_active, now())");
                 
           
            $parameters = array(
                'education_level' => $educationlevelObject->getEducationLevel(),
                'is_active' => $educationlevelObject->getIsActive(),
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
                $educationlevelObject->setId($newId);
            }

            //print_r($educationFieldsObject);
            //exit;
            
        }
    }
    
    public function getEducationlevel($id) {
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_level WHERE id=:id");
        $parameters = array(
            'id' => $id
        );
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new Educationlevels());
        }
    }
    
    public function getEducationlevelRadioList($status) {
//            Debug::dump($status);
//        exit;
//        if(empty($status)){
//        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_field");
//        $result = $statement->execute();
//        }
//        if(isset($status)){
//        Debug::dump($status);
//        exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_level  WHERE is_active=:is_active");
        $parameters = array(
            'is_active' => $status,
        );
        $result = $statement->execute($parameters);
        //$result = $statement->execute();
//        }
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Educationlevels());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }
    
    public function viewByEducationlevelId($table, $id) {

        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM $table WHERE id=:id");
        //$adapter->query('SELECT * FROM `artist` WHERE `id` = ?', array(5));

        $parameters = array(
            'id' => $id,
        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute($parameters);
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new Educationlevels());
        }
        //print_r($result->current());exit;
//        if ($result) {
//            $respArr = array('status' => "Deleted SuccessFully");getReligionList
//        } else {
//            $respArr = array('status' => "Couldn't deleted");
//        }
//
        //return $result;
    }
    
    public function educationLevelSearch($data) {
//        echo   "<pre>";
//        echo  $data;exit;
//        $field1 = empty($field) ? "" : "education_field like '" . $field . "%'";
        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_level WHERE education_level like '" . $data . "%'");
//        \Zend\Debug\Debug::dump($statement);exit;

//        $parameters = array(
//            'id' => $id,
//        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute();
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
        
         if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Educationlevels());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
       
    }
    
    public function performSearchEducationlevel($field) {
//        echo   "<pre>";
//        echo  $field;exit;
        $field1 = empty($field) ? "" : "education_level like '" . $field . "%'";
        //echo $id;exit;
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_level WHERE " . $field1 . "");
        

//        $parameters = array(
//            'id' => $id,
//        );
        //print_r($statement);
        ///exit;
        $result = $statement->execute();
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//            return $this->hydrator->hydrate($result->current(), new EducationFields());
//        }
        
         if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new Educationlevels());

            return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
       
    }
    
    
    
    
    
    

}
