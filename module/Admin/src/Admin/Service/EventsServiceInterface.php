<?php

namespace Admin\Service;

interface EventsServiceInterface {
    
    
    
    public function getAmmir();
    
    //added by amir
    public function test();
    
    public function customFieldsCity();
    
    //Events
    
    public function getEventsList();
    
    public function saveEvents($eventsObject);
    
    public function viewByEventsId($table, $id);
    
    public function getEvents($id);
    
    
    
    
    
    
    
    
}
