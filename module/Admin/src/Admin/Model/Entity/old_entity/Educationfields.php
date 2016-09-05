<?php

namespace Admin\Model\Entity;

class Educationfields {

    public $id;
    public $education_field;
    public $is_active;
    public $created_date;
    public $modified_date;
    public $modified_by;

    public function exchangeArray($data) {

        $this->id = (!empty($data['id'])) ? $data['id'] : null;

        $this->education_field = (!empty($data['education_field'])) ? $data['education_field'] : null;

        $this->is_active = (!empty($data['is_active'])) ? $data['is_active'] : null;

        $this->created_date = (!empty($data['created_date'])) ? $data['created_date'] : null;
        
        $this->modified_date = (!empty($data['modified_date'])) ? $data['modified_date'] : null;

        $this->modified_by = (!empty($data['modified_by'])) ? $data['modified_by'] : null;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
   