<?php

namespace Admin\Service;

use Admin\Mapper\EventsMapperInterface;
use Admin\Service\EventsServiceInterface;

class EventsService implements EventsServiceInterface {

    protected $eventsMapper;

    public function __construct(EventsMapperInterface $eventsMapper) {
        $this->eventsMapper=$eventsMapper;
    }
    
   
    
    public function getAmmir() {
        return $this->eventsMapper->getAmmir();
    }
    
    //added by amir
    public function test() {
        return $this->eventsMapper->test();
    }    
    
    public function customFieldsCity() {
        return $this->eventsMapper->customFieldsCity();
    }
    
    //Events
    
    public function getEventsList() {
        return $this->eventsMapper->getEventsList();
    }
    
    public function saveEvents($eventsObject) {
        return $this->eventsMapper->saveEvents($eventsObject);
    }
    
    public function viewByEventsId($table, $id) {
        return $this->eventsMapper->viewByEventsId($table, $id);
    }
    
    public function getEvents($id) {
        return $this->eventsMapper->getEvents($id);
    }
    
    
   
    
    
    
    
}
