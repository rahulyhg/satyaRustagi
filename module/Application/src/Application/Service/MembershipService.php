<?php

namespace Application\Service;

use Application\Service\MembershipServiceInterface;

class MembershipService implements MembershipServiceInterface{

    protected $commonMapper;

    public function __construct(\Application\Mapper\MembershipMapperInterface $commonMapper) {
        $this->commonMapper = $commonMapper;
    }

    public function findAllPosts() {
        return $this->commonMapper->findAll();
    }

    public function findPost($id) {
        return $this->commonMapper->find($id);
    }

    public function savePost(\Application\Model\Entity\MembershipInterface $data) {
        return $this->commonMapper->save($data);
    }

    public function deletePost(\Application\Model\Entity\MembershipInterface $data) {
        return $this->commonMapper->delete($data);
    }

}
