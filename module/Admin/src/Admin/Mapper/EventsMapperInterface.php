<?php

namespace Admin\Mapper;

interface EventsMapperInterface {

    
    
   public function getAmmir();
   
   public function getAmmirById($id);
   
   //added by amir
   public function test();   
   
   public function customFieldsCity();
   
   //Events   
   
   public function getEventsList();
   
   public function saveEvents($eventsObject);
   
   public function viewByEventsId($table, $id);
   
   public function getEvents($id);
   
   
   
   
   
   
   
   
}
