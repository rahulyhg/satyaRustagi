<?php

namespace Admin\Service;

use Admin\Mapper\AdminMapperInterface;
use Admin\Service\AdminServiceInterface;

class AdminService implements AdminServiceInterface {

    protected $adminMapper;

    public function __construct(AdminMapperInterface $adminMapper) {
        $this->adminMapper=$adminMapper;
    }

    public function getAmmir() {
        return $this->adminMapper->getAmmir();
    }
    
    public function getAmmirById($id) {
        return $this->adminMapper->getAmmirById($id);
    }
    
    public function saveEducationField($educationfieldEntity) {
        return $this->adminMapper->saveEducationField($educationfieldEntity);
    }
    
     public function getEducationFieldList($status) {
        return $this->adminMapper->getEducationFieldList($status);
    }
    
     public function getEducationFieldRadioList($status) {
        return $this->adminMapper->getEducationFieldRadioList($status);
    }
    
    public function changeStatus($table, $ids,  $data){
        return $this->adminMapper->changeStatus($table, $ids,  $data);
    }
    
    public function changeStatusAll($table, $ids, $data){
        return $this->adminMapper->changeStatusAll($table, $ids,  $data);
    }
    
    public function delete($table, $id){
        return $this->adminMapper->delete($table, $id);
    }
    
    public function deleteMultiple($table, $ids){
        return $this->adminMapper->deleteMultiple($table, $ids);
    }
    
    public function viewById($table, $id){
        return $this->adminMapper->viewById($table, $id);
    }

   

}
