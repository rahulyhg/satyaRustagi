<?php
namespace Admin\Form;

use Admin\Model\Entity\Cities;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;


class CityForm extends Form {

    public static $state_nameList = array();
    public static $country_nameList = array();
    


    public function __construct($name = null) {
        // we want to ignore the name passed
        parent::__construct('Cities');
        $this->setAttribute('method', 'post');
        $this->setHydrator(new ClassMethods());
        $this->setObject(new Cities());
        	
		$this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'city_name',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control',
                'id'=>'city_name'
            ),
            'options' => array(
                'label' => 'City Name',
            ),
        ));

        $this->add(array(
        'type' => 'Zend\Form\Element\Select',
        'name' => 'state_id',
        'attributes' => array(
                'class' => 'form-control',
                'id'=>'state_id'
        ),
        'options' => array(
            'label' => 'State Name',
        'empty_option' => 'Please Select State Name',
        'value_options' =>  self::$state_nameList,
        )
        ));

        $this->add(array(
        'type' => 'Zend\Form\Element\Select',
        'name' => 'country_id',
        'attributes' => array(
                'class' => 'form-control',
                'id'=>'country_id'
        ),
        'options' => array(
            'label' => 'Country Name',
            'disable_inarray_validator' => true,
        'empty_option' => 'Please Select Country Name',
        'value_options' =>  self::$country_nameList,
        )
        ));

        $this->add(array(
        'type' => 'Zend\Form\Element\Select',
        'name' => 'is_active',
        'attributes' => array(
                'class' => 'form-control',
                'id'=>'is_active'
        ),
        'options' => array(
            'label' => 'Status',
        'value_options' =>  array(
            '1'=>'Active',
            '0'=>'In Active'),
        )
        ));

		$this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Contact Us',
                'id' => 'submit',
                'class' => 'btn btn-default'
            ),
        ));    
         
    }

}
