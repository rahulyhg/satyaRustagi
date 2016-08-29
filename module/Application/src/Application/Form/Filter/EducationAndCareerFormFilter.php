<?php

namespace Application\Form\Filter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;

class EducationAndCareerFormFilter extends InputFilter {

    public function __construct() {
        // self::__construct(); // parnt::__construct(); - trows and error
        $this->add(array(
            'name' => 'education_level',
            'required' => true,
        ));

        $this->add(array(
            'name' => 'specialize_profession',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 10,
                    ),
                ),
//                array(
//                    'name' => 'not_empty',
//                ),
            ),
        ));
    }

}
