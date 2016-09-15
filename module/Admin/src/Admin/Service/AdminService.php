<?php

namespace Admin\Service;

use Admin\Mapper\AdminMapperInterface;
use Admin\Service\AdminServiceInterface;

class AdminService implements AdminServiceInterface {

    protected $adminMapper;

    public function __construct(AdminMapperInterface $adminMapper) {
        $this->adminMapper=$adminMapper;
    }
    
    //education field
    
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
    
    public function getEducationField($id) {
        return $this->adminMapper->getEducationField($id);
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
    
    
    public function performSearchEducationField($field){
        return $this->adminMapper->performSearchEducationField($field);
    }
    
    
    
    public function educationFieldSearch($data){
        return $this->adminMapper->educationFieldSearch($data);
    }
    
    //country    
    
    public function getCountriesList($status) {
        return $this->adminMapper->getCountriesList($status);
    }
    
    public function getCountryRadioList($status) {
        return $this->adminMapper->getCountryRadioList($status);
    }
    
    public function viewByCountryId($table, $id){
        return $this->adminMapper->viewByCountryId($table, $id);
    }
   
    public function saveCountry($countryEntity) {
        return $this->adminMapper->saveCountry($countryEntity);
    }
    
    public function getCountry($id) {
        return $this->adminMapper->getCountry($id);
    }
    
    public function performSearchCountry($field,$field2,$field3) {
        return $this->adminMapper->performSearchCountry($field,$field2,$field3);
    }
    
    public function countrySearch($data) {
        return $this->adminMapper->countrySearch($data);
    }
    
    //state
    
    public function getStatesList() {
        return $this->adminMapper->getStatesList();
    }
    
    public function customFields() {
        return $this->adminMapper->customFields();
    }
    
    
    public function performSearchState($field,$field2) {
        return $this->adminMapper->performSearchState($field,$field2);
    }
    
    public function saveState($stateObject) {
        return $this->adminMapper->saveState($stateObject);
    }
    
    public function getState($id) {
        return $this->adminMapper->getState($id);
    }
    
    public function getStateRadioList($status) {
        return $this->adminMapper->getStateRadioList($status);
    }
    
    public function viewByStateId($table, $id) {
        return $this->adminMapper->viewByStateId($table, $id);
    }
    
    public function stateSearch($data,$field) {
        return $this->adminMapper->stateSearch($data,$field);
    }
    
    
    //city
    public function getCitiesList() {
        return $this->adminMapper->getCitiesList();
    }
    
    public function getStateListByCountryCode($Country_ID) {
        return $this->adminMapper->getStateListByCountryCode($Country_ID);
    }
    
    public function getCityListByStateCode($State_ID) {
        return $this->adminMapper->getCityListByStateCode($State_ID);
    }
    
    public function getCityListByCountry($country_id) {
        return $this->adminMapper->getCityListByCountry($country_id);
    }
    
    public function getCityListByState($state_id) {
        return $this->adminMapper->getCityListByState($state_id);
    }
    
    public function getCityListByCity($city_id) {
        return $this->adminMapper->getCityListByCity($city_id);
    }
    
    public function customFieldsState() {
        return $this->adminMapper->customFieldsState();
    }
    
    public function SaveCity($cityObject) {
        return $this->adminMapper->SaveCity($cityObject);
    }
    
    public function getCity($id) {
        return $this->adminMapper->getCity($id);
    }
    
    public function getCityRadioList($status) {
        return $this->adminMapper->getCityRadioList($status);
    }
    
    public function viewByCityId($table, $id) {
        return $this->adminMapper->viewByCityId($table, $id);
    }
    
    
    
    
    //Religion
    
    
    public function getReligionList($status) {
        return $this->adminMapper->getReligionList($status);
    }
    
    public function SaveReligion($religionObject) {
        return $this->adminMapper->SaveReligion($religionObject);
    }
    
    public function getReligion($id) {
        return $this->adminMapper->getReligion($id);
    }
    
    public function religionSearch($data) {
        return $this->adminMapper->religionSearch($data);
    }
    
    public function performSearchReligion($field) {
        return $this->adminMapper->performSearchReligion($field);
    }
    
    public function getReligionRadioList($status) {
        return $this->adminMapper->getReligionRadioList($status);
    }
    
    public function viewByReligionId($table, $id) {
        return $this->adminMapper->viewByReligionId($table, $id);
    }
    
    //Gothras
    
    public function getGothrasList($status) {
        return $this->adminMapper->getGothrasList($status);
    }
    
    public function SaveGothra($gothraObject) {
        return $this->adminMapper->SaveGothra($gothraObject);
    }
    
    public function getGothra($id) {
        return $this->adminMapper->getGothra($id);
    }
    
    public function gothraSearch($data) {
        return $this->adminMapper->gothraSearch($data);
    }
    
    public function performSearchGothra($field) {
        return $this->adminMapper->performSearchGothra($field);
    }
    
    public function getGothraRadioList($status) {
        return $this->adminMapper->getGothraRadioList($status);
    }
    
    public function viewByGothraId($table, $id) {
        return $this->adminMapper->viewByGothraId($table, $id);
    }
    
    //Starsign
    
    
    public function getStarsignList($status) {
        return $this->adminMapper->getStarsignList($status);
    }
    
    public function SaveStarsign($starsignObject) {
        return $this->adminMapper->SaveStarsign($starsignObject);
    }
    
    public function getStarsign($id) {
        return $this->adminMapper->getStarsign($id);
    }
    
    public function starsignSearch($data) {
        return $this->adminMapper->starsignSearch($data);
    }
    
    public function performSearchStarsign($field) {
        return $this->adminMapper->performSearchStarsign($field);
    }
    
    public function getStarsignRadioList($status) {
        return $this->adminMapper->getStarsignRadioList($status);
    }
    
    public function viewByStarsignId($table, $id) {
        return $this->adminMapper->viewByStarsignId($table, $id);
    }
    
    //Zodiacsign
    
    public function getZodiacsignList($status) {
        return $this->adminMapper->getZodiacsignList($status);
    }
    
    public function SaveZodiacsign($zodiacsignObject) {
        return $this->adminMapper->SaveZodiacsign($zodiacsignObject);
    }
    
    public function getZodiacsign($id) {
        return $this->adminMapper->getZodiacsign($id);
    }
    
    public function zodiacsignSearch($data) {
        return $this->adminMapper->zodiacsignSearch($data);
    }
    
    public function performSearchZodiacsign($field) {
        return $this->adminMapper->performSearchZodiacsign($field);
    }
    
    public function getZodiacsignRadioList($status) {
        return $this->adminMapper->getZodiacsignRadioList($status);
    }
    
    public function viewByZodiacsignId($table, $id) {
        return $this->adminMapper->viewByZodiacsignId($table, $id);
    }
    
    //Profession
    
    public function getProfessionList($status) {
        return $this->adminMapper->getProfessionList($status);
    }
    
    public function SaveProfession($id) {
        return $this->adminMapper->SaveProfession($id);
    }
    
    public function getProfession($id) {
        return $this->adminMapper->getProfession($id);
    }
    
    public function professionSearch($data) {
        return $this->adminMapper->professionSearch($data);
    }
    
    public function performSearchProfession($field) {
        return $this->adminMapper->performSearchProfession($field);
    }
    
    public function getProfessionRadioList($status) {
        return $this->adminMapper->getProfessionRadioList($status);
    }
    
    public function viewByProfessionId($table, $id) {
        return $this->adminMapper->viewByProfessionId($table, $id);
    }
    
    //Designation
    
    public function getDesignationList($status) {
        return $this->adminMapper->getDesignationList($status);
    }
    
    public function SaveDesignation($designationObject) {
        return $this->adminMapper->SaveDesignation($designationObject);
    }
    
    public function getDesignation($id) {
        return $this->adminMapper->getDesignation($id);
    }
    
    public function designationSearch($data) {
        return $this->adminMapper->designationSearch($data);
    }
    
    public function performSearchDesignation($field) {
        return $this->adminMapper->performSearchDesignation($field);
    }
    
    public function getDesignationRadioList($status) {
        return $this->adminMapper->getDesignationRadioList($status);
    }
    
    public function viewByDesignationId($table, $id) {
        return $this->adminMapper->viewByDesignationId($table, $id);
    }
    
    //Education level
    
    public function getEducationlevelList($status) {
        return $this->adminMapper->getEducationlevelList($status);
    }
    
    public function SaveEducationlevel($educationlevelObject) {
        return $this->adminMapper->SaveEducationlevel($educationlevelObject);
    }
    
    public function getEducationlevel($id) {
        return $this->adminMapper->getEducationlevel($id);
    }
    
    public function getEducationlevelRadioList($status) {
        return $this->adminMapper->getEducationlevelRadioList($status);
    }
    
    public function viewByEducationlevelId($table, $id) {
        return $this->adminMapper->viewByEducationlevelId($table, $id);
    }
    
    public function educationLevelSearch($data) {
        return $this->adminMapper->educationLevelSearch($data);
    }
    
    public function performSearchEducationlevel($field) {
        return $this->adminMapper->performSearchEducationlevel($field);
    }
    
    
    
}
