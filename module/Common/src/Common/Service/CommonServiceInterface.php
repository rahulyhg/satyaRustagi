<?php

namespace Common\Service;

interface CommonServiceInterface {

    public function getUserById($id);

    public function getUserType();

    public function getAnnualIncomeList();

    public function getCountryList();

    public function getCountryById($id);

    public function getStateList();

    public function getStateById();

    public function getStateListByCountryCode($countryId);

    public function getCityList();
    
    public function getCityById();

    public function getCityListByStateCode($stateId);
    
    public function getBrachListByCity($cityId);

    public function getDesignationList();

    public function getEducationFieldList();

    public function getGotraList();

    public function getHeightList();

    public function getProfessionList();

    public function getReligionList();

    public function getEducationLevelList();

    public function getBloodGroupList();

    public function getMeritalStatusList();

    public function getEmploymentStatusList();

    public function getLiveStatusList();

    public function getWorkingWithCompanyList();

    public function getAge();

    public function getAffluenceLevelStatusList();

    public function getFamilyValuesStatusList();

    public function getNameTitleList();

    public function getGothraList();

    public function getRustagiBranchList();

    public function getPostCategoryList();
    
    public function bindAccountForm($formObject, $modelObject);
    
    public function profileForList();
    
     public function genderList();

    public function disabilityList();

    public function bodyTypeList();

    public function skinToneList();
    
}
