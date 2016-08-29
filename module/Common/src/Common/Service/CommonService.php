<?php

namespace Common\Service;

use Common\Mapper\CommonMapperInterface;
use Common\Service\CommonServiceInterface;

class CommonService implements CommonServiceInterface {

    protected $commonMapper;

    public function __construct(CommonMapperInterface $commonMapper) {
        $this->commonMapper = $commonMapper;
    }

    public function getUserById($id) {
        return $this->commonMapper->getUserById($id);
    }

    public function getUserType() {
        return $this->commonMapper->getUserType();
    }

    public function getAge() {
        return $this->commonMapper->getAge();
    }

    public function getCountryList() {
        return $this->commonMapper->getCountryList();
    }

    public function getCountryById($id) {
        return $this->commonMapper->getCountryById($id);
    }

    public function getStateList() {
        return $this->commonMapper->getStateList();
    }

    public function getStateById() {
        return $this->commonMapper->getStateById();
    }

    public function getStateListByCountryCode($countryId) {
        return $this->commonMapper->getStateListByCountryCode($countryId);
    }

    public function getCityList() {
        return $this->commonMapper->getCityList();
    }

    public function getCityById() {
        return $this->commonMapper->getCityById();
    }

    public function getCityListByStateCode($stateId) {
        return $this->commonMapper->getCityListByStateCode($stateId);
    }

    public function getAnnualIncomeList() {
        return $this->commonMapper->getAnnualIncomeList();
    }

    public function getDesignationList() {
        return $this->commonMapper->getDesignationList();
    }

    public function getEducationFieldList() {
        return $this->commonMapper->getEducationFieldList();
    }

    public function getEducationLevelList() {
        return $this->commonMapper->getEducationLevelList();
    }

    public function getGotraList() {
        return $this->commonMapper->getGotraList();
    }

    public function getHeightList() {
        return $this->commonMapper->getHeightList();
    }

    public function getProfessionList() {
        return $this->commonMapper->getProfessionList();
    }

    public function getReligionList() {
        return $this->commonMapper->getReligionList();
    }

    public function getAffluenceLevelStatusList() {
        return $this->commonMapper->getAffluenceLevelStatusList();
    }

    public function getBloodGroupList() {
        return $this->commonMapper->getBloodGroupList();
    }

    public function getEmploymentStatusList() {
        return $this->commonMapper->getEmploymentStatusList();
    }

    public function getFamilyValuesStatusList() {
        return $this->commonMapper->getFamilyValuesStatusList();
    }

    public function getNameTitleList() {
        return $this->commonMapper->getNameTitleList();
    }

    public function getGothraList() {
        return $this->commonMapper->getGotraList();
    }

    public function getLiveStatusList() {
        return $this->commonMapper->getLiveStatusList();
    }

    public function getMeritalStatusList() {
        return $this->commonMapper->getMeritalStatusList();
    }

    public function getPostCategoryList() {
        return $this->commonMapper->getPostCategoryList();
    }

    public function getRustagiBranchList() {
        return $this->commonMapper->getRustagiBranchList();
    }

    public function getWorkingWithCompanyList() {
        return $this->commonMapper->getWorkingWithCompanyList();
    }

    public function getBrachListByCity($cityId) {
        return $this->commonMapper->getBrachListByCity($cityId);
    }

    public function bindAccountForm($formObject, $modelObject) {
        return $this->commonMapper->bindAccountForm($formObject, $modelObject);
    }

    public function profileForList() {
        return $this->commonMapper->profileForList();
    }

    public function genderList() {
        return $this->commonMapper->genderList();
    }

    public function disabilityList() {
        return $this->commonMapper->disabilityList();
    }

    public function bodyTypeList() {
        return $this->commonMapper->bodyTypeList();
    }

    public function skinToneList() {
        return $this->commonMapper->skinToneList();
    }

}
