<?php
namespace Admin\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;

class GothraFilter extends InputFilter {

    public function __construct() {
        	
		

        $this->add(array(
            'name' => 'gothra_name',
            'required'=> true,
        ));

        $this->add(array(
            'name' => 'IsActive',
            'required'=> true,
        ));

    }

}
