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
    
     public function getEducationFieldList() {
        return $this->adminMapper->getEducationFieldList();
    }
    
    public function changeStatus($table, $ids,  $data){
        return $this->adminMapper->changeStatus($table, $ids,  $data);
    }
    
    public function changeStatusAll($table, $ids, $data){
        return $this->adminMapper->changeStatusAll($table, $ids,  $data);
    }
    
    public function delete($table, $id){
        return $this->adminMapper->delete($table, $ids);
    }
    
    public function deleteMultiple($table, $ids){
        return $this->adminMapper->deleteMultiple($table, $ids);
    }

   

}
