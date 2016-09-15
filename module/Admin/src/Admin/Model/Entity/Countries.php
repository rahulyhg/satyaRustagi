<?php

namespace Admin\Model\Entity;

class Countries {

    public $id;
    public $countryName;
    public $isActive;
    public $dialCode;
    public $countryCode;
    public $createdDate;
    public $modifiedDate;
    public $modifiedBy;
    public $masterCountryId;

//    public function exchangeArray($data) {
//
//        $this->id = (!empty($data['id'])) ? $data['id'] : null;
//
//        $this->country_name = (!empty($data['country_name'])) ? $data['country_name'] : null;
//
//        $this->IsActive = (!empty($data['IsActive'])) ? $data['IsActive'] : null;
//
//        $this->dial_code = (!empty($data['dial_code'])) ? $data['dial_code'] : null;
//
//        $this->country_code = (!empty($data['country_code'])) ? $data['country_code'] : null;
//
//        $this->created_date = (!empty($data['created_date'])) ? $data['created_date'] : null;
//        
//        $this->modified_date = (!empty($data['modified_date'])) ? $data['modified_date'] : null;
//
//        $this->modified_by = (!empty($data['modified_by'])) ? $data['modified_by'] : null;
//        
//        $this->master_country_id = (!empty($data['master_country_id'])) ? $data['master_country_id'] : null;
//    }
//
//    public function getArrayCopy() {
//        return get_object_vars($this);
//    }
    
    function getId() {
        return $this->id;
    }

    function getCountryName() {
        return $this->countryName;
    }

    function getIsActive() {
        return $this->isActive;
    }

    function getDialCode() {
        return $this->dialCode;
    }

    function getCountryCode() {
        return $this->countryCode;
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

    function getMasterCountryId() {
        return $this->masterCountryId;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setCountryName($countryName) {
        $this->countryName = $countryName;
    }

    function setIsActive($isActive) {
        $this->isActive = $isActive;
    }

    function setDialCode($dialCode) {
        $this->dialCode = $dialCode;
    }

    function setCountryCode($countryCode) {
        $this->countryCode = $countryCode;
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

    function setMasterCountryId($masterCountryId) {
        $this->masterCountryId = $masterCountryId;
    }



}
   