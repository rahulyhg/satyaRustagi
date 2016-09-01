<?php

namespace Application\Service;

use Application\Form\Entity\SingUpFormInterface;
use Application\Mapper\UserMapperInterface;
use Application\Model\Entity\PersonalDetailsInterface;
use Application\Service\UserServiceInterface;

class UserService implements UserServiceInterface {

    protected $userMapper;

    public function __construct(UserMapperInterface $userMapper) {
        $this->userMapper = $userMapper;
    }

    public function getUserById($id) {
        return $this->userMapper->getUserById($id);
    }

    public function getUserInfoById($id, $columns=false) {
        return $this->userMapper->getUserInfoById($id, $columns);
    }
    
    public function getUserProfessionById($id, $columns=false){
        return $this->userMapper->getUserProfessionById($id, $columns);
    }
    
    public function getUserEducationAndCareerDetailById($id, $columns=false){
        return $this->userMapper->getUserEducationAndCareerDetailById($id, $columns);
    }
    
    public function saveUserEducationAndCareerDetail($educationAndCareerData){
        return $this->userMapper->saveUserEducationAndCareerDetail($educationAndCareerData);
    }
    
    public function userSummaryById($id){
        return $this->userMapper->userSummaryById($id);
    }
    
    public function getUserAboutById($id){
        return $this->userMapper->getUserAboutById($id);
    }
    
    public function saveUserAbout($userAboutData){
        return $this->userMapper->saveUserAbout($userAboutData);
    }
    
    public function getUserPersonalDetailById($id){
        return $this->userMapper->getUserPersonalDetailById($id);
    }
    
    public function educationDetailById($id){
        return $this->userMapper->educationDetailById($id);
    }

    public function checkAlreadyExist($fieldName, $value) {
        return $this->userMapper->checkAlreadyExist($fieldName, $value);
    }

    public function getMemberInfoById($id) {
        return $this->userMapper->getMemberInfoById($id);
    }

    public function getRegisteredUserByActivationCode($id, $activationCode) {
        return $this->userMapper->getRegisteredUserByActivationCode($id, $activationCode);
    }

    public function getRegisteredUserById($id) {
        return $this->userMapper->getRegisteredUserById($id);
    }

    public function getUserCareerById($id) {
        return $this->userMapper->getUserCareerById($id);
    }

    public function getUserEducationById($id) {
        return $this->userMapper->getUserEducationById($id);
    }

    public function getUserMatrimonialById($id) {
        return $this->userMapper->getUserMatrimonialById($id);
    }

    public function removeUser($id) {
        return $this->userMapper->removeUser($id);
    }

    public function saveUser($object) {
        return $this->userMapper->saveUser($object);
    }

    public function saveUserSignUp(SingUpFormInterface $userObject) {
        return $this->userMapper->saveUserSignUp($userObject);
    }
    
    public function saveUserPersonalDetails($personalDetailsObject){
        return $this->userMapper->saveUserPersonalDetails($personalDetailsObject);
    }
    
    public function saveUserEducationDetails($educationDetailsData){
        return $this->userMapper->saveUserEducationDetails($educationDetailsData);
    }
    
    
    
    public function saveUserProfessionDetails($professionDetailsData){
        return $this->userMapper->saveUserProfessionDetails($educationDetailsData);
    }
    
    public function saveAcitivationSmsCode($userId, $number, $code, $time){
        return $this->userMapper->saveAcitivationSmsCode($userId, $number, $code, $time);
    }

    public function saveUserCareer($careerInfo) {
        return $this->userMapper->saveUserCareer($careerInfo);
    }

    public function saveUserEducation($educationInfo) {
        return $this->userMapper->saveUserEducation($educationInfo);
    }

    public function saveUserInfo($infoData) {
        return $this->userMapper->saveUserInfo($infoData);
    }

    public function saveUserMatrimonial($matrimonialInfo) {
        return $this->userMapper->saveUserMatrimonial($matrimonialInfo);
    }
    
    public function ProfileBar($user_id){
        return $this->userMapper->ProfileBar($user_id);
    }
    
    public function getUserPostById($user_id){
        return $this->userMapper->getUserPostById($user_id);
    }
    
    public function saveUserPost($userPostData){
        return $this->userMapper->saveUserPost($userPostData);
    }

}
