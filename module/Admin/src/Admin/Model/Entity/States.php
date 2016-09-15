<?php

namespace Admin\Model\Entity;

class States {

    public $id;
    public $stateName;
    public $isActive;
    public $countryId;
    //public $masterStateId;
    public $countryName;
    public $createdDate;
    public $modifiedDate;
    public $modifiedBy;

//    public function exchangeArray($data) {
//
//        $this->id = (!empty($data['id'])) ? $data['id'] : null;
//
//        $this->state_name = (!empty($data['state_name'])) ? $data['state_name'] : null;
//
//        $this->IsActive = (!empty($data['IsActive'])) ? $data['IsActive'] : null;
//
//        $this->country_id = (!empty($data['country_id'])) ? $data['country_id'] : null;
//        
//        //$this->master_state_id = (!empty($data['master_state_id'])) ? $data['master_state_id'] : null;
//
//        $this->created_date = (!empty($data['created_date'])) ? $data['created_date'] : null;
//        
//        $this->modified_date = (!empty($data['modified_date'])) ? $data['modified_date'] : null;
//
//        $this->modified_by = (!empty($data['modified_by'])) ? $data['modified_by'] : null;
//        
//        $this->country_name = (!empty($data['country_name'])) ? $data['country_name'] : null;
//    }
//
//    public function getArrayCopy() {
//        return get_object_vars($this);
//    }
    function getId() {
        return $this->id;
    }

    function getStateName() {
        return $this->stateName;
    }

    function getIsActive() {
        return $this->isActive;
    }

    function getCountryId() {
        return $this->countryId;
    }

    function getCountryName() {
        return $this->countryName;
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

    function setStateName($stateName) {
        $this->stateName = $stateName;
    }

    function setIsActive($isActive) {
        $this->isActive = $isActive;
    }

    function setCountryId($countryId) {
        $this->countryId = $countryId;
    }

    function setCountryName($countryName) {
        $this->countryName = $countryName;
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
   