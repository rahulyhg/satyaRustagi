<?php

namespace Admin\Mapper;

interface AdminMapperInterface {

    //education field
    
   public function getAmmir();
   
   public function getAmmirById($id);
   
   public function saveEducationField($educationfieldEntity);
   
   public function getEducationFieldList($status);
   
   public function getEducationField($id);
   
   public function getEducationFieldRadioList($status);
   
   public function changeStatus($table, $ids, $data);
   
   public function changeStatusAll($table, $ids, $data);
   
   public function delete($table, $id);
   
   public function deleteMultiple($table, $ids);
   
   public function viewById($table, $id);
   
   public function performSearchEducationField($field);
   
   public function educationFieldSearch($data);
   
   //country 
   
   public function getCountriesList($status);
   
   public function getCountryRadioList($status);
   
   public function viewByCountryId($table, $id);
   
   public function saveCountry($countryEntity);
   
   public function getCountry($id);
   
   public function performSearchCountry($field,$field2,$field3);
   
   public function countrySearch($data);
   
   //state
   
   public function getStatesList();
   
   public function customFields();
   
   public function performSearchState($field,$field2);   
   
   public function saveState($stateObject);
   
   public function getState($id);
   
   public function getStateRadioList($status);
   
   public function viewByStateId($table, $id);
   
   public function stateSearch($data,$field);
   
   
   //city
   
   public function getCitiesList();
   
   public function getStateListByCountryCode($Country_ID);
   
   public function getCityListByStateCode($State_ID);
   
   public function getCityListByCountry($country_id);
   
   public function getCityListByState($state_id);
   
   public function getCityListByCity($city_id);
   
   public function customFieldsState();
   
   public function SaveCity($cityObject);
   
   public function getCity($id);
   
   public function getCityRadioList($status);
   
   public function viewByCityId($table, $id);
   
   
   
   
   
   //Religion
   
   public function getReligionList($status);
   
   public function SaveReligion($religionObject);
   
   public function getReligion($id);
   
   public function religionSearch($data);
   
   public function performSearchReligion($field);
   
   public function getReligionRadioList($status);
   
   public function viewByReligionId($table, $id);
   
   //Gothras
   
   public function getGothrasList($status);
   
   public function SaveGothra($gothraObject);
   
   public function getGothra($id);
   
   public function gothraSearch($data);
   
   public function performSearchGothra($field);
   
   public function getGothraRadioList($status);
   
   public function viewByGothraId($table, $id);
   
   //Starsign
   
   
   public function getStarsignList($status);
   
   public function SaveStarsign($starsignObject);
   
   public function getStarsign($id);
   
   public function starsignSearch($data);
   
   public function performSearchStarsign($field);
   
   public function getStarsignRadioList($status);
   
   public function viewByStarsignId($table, $id);
   
   //Zodiacsign
   
   
   public function getZodiacsignList($status);
   
   public function SaveZodiacsign($zodiacsignObject);
   
   public function getZodiacsign($id);
   
   public function zodiacsignSearch($data);
   
   public function performSearchZodiacsign($field);
   
   public function getZodiacsignRadioList($status);
   
   public function viewByZodiacsignId($table, $id);
   
   //Profession
   
   public function getProfessionList($status);
   
   public function SaveProfession($professionObject);
   
   public function getProfession($id);
   
   public function professionSearch($data);
   
   public function performSearchProfession($field);
   
   public function getProfessionRadioList($status);
   
   public function viewByProfessionId($table, $id);
   
   //Designation
   
   public function getDesignationList($status);
   
   public function SaveDesignation($designationObject);
   
   public function getDesignation($id);
   
   public function designationSearch($data);
   
   public function performSearchDesignation($field);
   
   public function getDesignationRadioList($status);
   
   public function viewByDesignationId($table, $id);
   
   //Education level
   
   public function getEducationlevelList($status);
   
   public function SaveEducationlevel($educationlevelObject);
   
   public function getEducationlevel($id);
   
   public function getEducationlevelRadioList($status);
   
   public function viewByEducationlevelId($table, $id);
   
   public function educationLevelSearch($data);
   
   public function performSearchEducationlevel($field);
   
   
   
   
}
