<?php

namespace Admin\Model\Entity;

class Religions {

    public $id;
    public $religion_name;
    public $IsActive;
    public $created_date;
    public $modified_date;
    public $modified_by;

    public function exchangeArray($data) {

        $this->id = (!empty($data['id'])) ? $data['id'] : null;

        $this->religion_name = (!empty($data['religion_name'])) ? $data['religion_name'] : null;

        $this->IsActive = (!empty($data['IsActive'])) ? $data['IsActive'] : null;

        $this->created_date = (!empty($data['created_date'])) ? $data['created_date'] : null;
        
        $this->modified_date = (!empty($data['modified_date'])) ? $data['modified_date'] : null;

        $this->modified_by = (!empty($data['modified_by'])) ? $data['modified_by'] : null;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
   