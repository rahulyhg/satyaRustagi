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

    public function getEducationFieldList() {
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_education_field");
        $result = $statement->execute();
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $resultSet = new HydratingResultSet($this->hydrator, new EducationFields());

         return $resultSet->initialize($result);
            //return $this->hydrator->hydrate($result->current(), new EducationFields());
        }
    }

    public function saveEducation($educationObject) {
        $educationData = $this->hydrator->extract($educationObject);
        $educationData = array_filter((array) $educationData, function ($val) {
            return !is_null($val);
        });

//        $userData['about_yourself_partner_family']=$userData['about_me'];
        //unset($userData['about_me']);
        Debug::dump($educationData);
        exit;
        $sql = new Sql($this->dbAdapter);
        $action = new Update('tbl_user_info');
        $action->set($userData);
        $action->where(array('id = ?' => $postData['id']));
        $stmt = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();
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
        $statement = $this->dbAdapter->query("UPDATE $table set is_active=:is_active where id IN (:ids)");
        $parameters = array(
            'ids' => $ids,
            'is_active' => $data['is_active']
        );
        $result = $statement->execute($parameters);
    }

    public function delete($table, $id) {
        $statement = $this->dbAdapter->query("DELETE $table where id=:id");
        $parameters = array(
            'id' => $id,
        );
        $result = $statement->execute($parameters);
    }

    public function deleteMultiple($table, $ids) {
        $statement = $this->dbAdapter->query("DELETE $table where id IN(:ids)");
        $parameters = array(
            'ids' => $ids,
        );
        $result = $statement->execute($parameters);
    }

}
