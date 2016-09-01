<?php

namespace Admin\Model\Entity;

class EducationFields {

    public $id;
    public $educationField;
    public $isActive;
    public $createdDate;
    public $modifiedDate;
    public $modifiedBy;

    function getId() {
        return $this->id;
    }

    function getEducationField() {
        return $this->educationField;
    }

    function getIsActive() {
        return $this->isActive;
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

    function setEducationField($educationField) {
        $this->educationField = $educationField;
    }

    function setIsActive($isActive) {
        $this->isActive = $isActive;
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
   