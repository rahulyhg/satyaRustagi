<?php

namespace Admin\Model\Entity;

class Cities {

    public $id;
    public $cityName;
    public $isActive;
    public $stateId;
    public $countryId;
    public $stateName;
    public $createdDate;
    public $modifiedDate;
    public $modifiedBy;

//    public function exchangeArray($data) {
//
//        $this->id = (!empty($data['id'])) ? $data['id'] : null;
//
//        $this->city_name = (!empty($data['city_name'])) ? $data['city_name'] : null;
//
//        $this->IsActive = (!empty($data['IsActive'])) ? $data['IsActive'] : null;
//
//        $this->state_id = (!empty($data['state_id'])) ? $data['state_id'] : null;
//        
//        $this->country_id = (!empty($data['country_id'])) ? $data['country_id'] : null;
//
//        $this->created_date = (!empty($data['created_date'])) ? $data['created_date'] : null;
//        
//        $this->modified_date = (!empty($data['modified_date'])) ? $data['modified_date'] : null;
//
//        $this->modified_by = (!empty($data['modified_by'])) ? $data['modified_by'] : null;
//        
//        $this->state_name = (!empty($data['state_name'])) ? $data['state_name'] : null;
//    }
//
//    public function getArrayCopy() {
//        return get_object_vars($this);
//    }
    function getId() {
        return $this->id;
    }

    function getCityName() {
        return $this->cityName;
    }

    function getIsActive() {
        return $this->isActive;
    }

    function getStateId() {
        return $this->stateId;
    }

    function getCountryId() {
        return $this->countryId;
    }

    function getStateName() {
        return $this->stateName;
    }

    function getCreatedDate() {
        return $this->createdDate;
    }

    function getModifiedDate() {
        return $this->modifiedDate;
    }

    function getModifiedBy() {
        return $this->modifiedBy;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setCityName($cityName) {
        $this->cityName = $cityName;
    }

    function setIsActive($isActive) {
        $this->isActive = $isActive;
    }

    function setStateId($stateId) {
        $this->stateId = $stateId;
    }

    function setCountryId($countryId) {
        $this->countryId = $countryId;
    }

    function setStateName($stateName) {
        $this->stateName = $stateName;
    }

    function setCreatedDate($createdDate) {
        $this->createdDate = $createdDate;
    }

    function setModifiedDate($modifiedDate) {
        $this->modifiedDate = $modifiedDate;
    }

    function setModifiedBy($modifiedBy) {
        $this->modifiedBy = $modifiedBy;
    }



}
   