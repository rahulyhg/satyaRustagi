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

    

}
