<?php

namespace Admin\Mapper;

use Admin\Model\Entity\EducationFields;
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
    
    

    public function saveEducationField($educationfieldEntity) {
        
        echo  "<pre>";
    print_r($educationfieldEntity['education_field']);exit;
    $created_date = (empty($educationfieldEntity->created_date))? date('Y-m-d h:i:s'):$educationfieldEntity->created_date;
                $educationfieldEntity->modified_date = (empty($educationfieldEntity->modified_date))? date('Y-m-d h:i:s'):$educationfieldEntity->modified_date;
        
         $statement = $this->dbAdapter->query("UPDATE $table SET is_active=:is_active WHERE id=:id");
        //Debug::dump($id);
        //exit;
        $parameters = array(
            'id' => $id,
            'is_active' => $data['is_active']
        );
        $result = $statement->execute($parameters);

        if ($result) {
            $respArr = array('status' => "success");
        } else {
            $respArr = array('status' => "couldn't update");
        }

        return $respArr;
//        $educationData = $this->hydrator->extract($educationObject);
//        $educationData = array_filter((array) $educationData, function ($val) {
//            return !is_null($val);
//        });
//
////        $userData['about_yourself_partner_family']=$userData['about_me'];
//        //unset($userData['about_me']);
//        Debug::dump($educationData);
//        exit;
//        $sql = new Sql($this->dbAdapter);
//        $action = new Update('tbl_user_info');
//        $action->set($userData);
//        $action->where(array('id = ?' => $postData['id']));
//        $stmt = $sql->prepareStatementForSqlObject($action);
//        $result = $stmt->execute();
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
        $ids=$this->dbAdapter->getPlatform()->quoteValueList(explode(",",$ids));
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
        $ids=$this->dbAdapter->getPlatform()->quoteValueList(explode(",",$ids));
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

}
