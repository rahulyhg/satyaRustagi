<?php

namespace Application\Service;

use Application\Service\MatrimonialServiceInterface;

class MatrimonialService implements MatrimonialServiceInterface{

    protected $commonMapper;

    public function __construct(\Application\Mapper\MatrimonialMapperInterface $commonMapper) {
        $this->commonMapper = $commonMapper;
    }

    public function findAllPosts() {
        return $this->commonMapper->findAll();
    }

    public function findPost($id) {
        return $this->commonMapper->find($id);
    }

    public function savePost(\Application\Model\Entity\MatrimonialInterface $data) {
        return $this->commonMapper->save($data);
    }

    public function deletePost(\Application\Model\Entity\MatrimonialInterface $data) {
        return $this->commonMapper->delete($data);
    }

}
