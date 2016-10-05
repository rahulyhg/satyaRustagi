<?php

namespace Admin\Model\Entity;

class Events {

//    public $id;
//    public $event_name;
//    public $event_date;
//    public $event_desc;
//    public $ip;
//    public $created_by;
//    public $IsActive;
//    public $country_id;
//    public $state_id;
//    public $city_id;
//    public $country_name;
//    public $state_name;
//    public $city_name;
//    public $venue;
//    public $image;
//    public $start_time;
//    public $end_time;
//    public $end_date;
//    public $event_organiser;
//    public $created_date;
//    public $modified_date;
//    public $organiser_photo;
//    public $sponser_name;
//    public $sponser_photo;
//    public $event_cost;
//    public $event_members;
//    public $sponser_contact;
//    public $organiser_contact;
//
//    public function exchangeArray($data) {
//
//        $this->id = (!empty($data['id'])) ? $data['id'] : null;
//
//        $this->event_name = (!empty($data['event_name'])) ? $data['event_name'] : null;
//
//        // $this->event_date = (!empty($data['event_date'])) ? $data['event_date'] : null;
//
//        $this->event_desc = (!empty($data['event_desc'])) ? $data['event_desc'] : null;
//
//        // $this->ip = (!empty($data['ip'])) ? $data['ip'] : null;
//
//        $this->created_by = (!empty($data['created_by'])) ? $data['created_by'] : null;
//
//        $this->IsActive = (!empty($data['IsActive'])) ? $data['IsActive'] : null;
//
//        $this->country_id = (!empty($data['country_id'])) ? $data['country_id'] : null;
//
//        $this->state_id = (!empty($data['state_id'])) ? $data['state_id'] : null;
//
//        $this->city_id = (!empty($data['city_id'])) ? $data['city_id'] : null;
//
//        $this->country_name = (!empty($data['country_name'])) ? $data['country_name'] : null;
//
//        $this->state_name = (!empty($data['state_name'])) ? $data['state_name'] : null;
//        $this->city_name = (!empty($data['city_name'])) ? $data['city_name'] : null;
//
//        $this->image = (!empty($data['image'])) ? $data['image'] : null;
//        
//        $this->venue = (!empty($data['venue'])) ? $data['venue'] : null;
//
//        $this->start_time = (!empty($data['start_time'])) ? $data['start_time'] : null;
//
//        $this->event_date = (!empty($data['event_date'])) ? $data['event_date'] : null;
//
//        $this->end_time = (!empty($data['end_time'])) ? $data['end_time'] : null;
//
//        $this->end_date = (!empty($data['end_date'])) ? $data['end_date'] : null;
//
//        $this->created_date = (!empty($data['created_date'])) ? $data['created_date'] : null;
//        
//        $this->modified_date = (!empty($data['modified_date'])) ? $data['modified_date'] : null;
//
//        $this->event_organiser = (!empty($data['event_organiser'])) ? $data['event_organiser'] : null;
//        
//        $this->organiser_photo = (!empty($data['organiser_photo'])) ? $data['organiser_photo'] : null;
//        $this->sponser_name = (!empty($data['sponser_name'])) ? $data['sponser_name'] : null;
//        $this->sponser_photo = (!empty($data['sponser_photo'])) ? $data['sponser_photo'] : null;
//        $this->event_cost = (!empty($data['event_cost'])) ? $data['event_cost'] : null;
//        $this->event_members = (!empty($data['event_members'])) ? $data['event_members'] : null;
//        $this->organiser_contact = (!empty($data['organiser_contact'])) ? $data['organiser_contact'] : null;
//        $this->sponser_contact = (!empty($data['sponser_contact'])) ? $data['sponser_contact'] : null;
//        
//    }
//
//    public function getArrayCopy() {
//        return get_object_vars($this);
//    }

    public $id;
    public $eventName;
    public $eventDate;
    public $eventDesc;
    public $isActive;
    public $createdBy;
    public $ip;
    public $createdDate;
    public $modifiedDate;
    public $countryId;
    public $cityId;
    public $countryName;
    public $stateName;
    public $cityName;
    public $venue;
    public $image;
    public $startTime;
    public $endTime;
    public $eventOrganiser;
    public $modifiedBy;
    public $organiserPhoto;
    public $sponserName;
    public $sponserPhoto;
    public $eventCost;
    public $eventMembers;
    public $stateId;
    public $sponserContact;
    public $organiserContact;
    public $endDate;
    
    function getId() {
        return $this->id;
    }

    function getEventName() {
        return $this->eventName;
    }

    function getEventDate() {
        return $this->eventDate;
    }

    function getEventDesc() {
        return $this->eventDesc;
    }

    function getIsActive() {
        return $this->isActive;
    }

    function getCreatedBy() {
        return $this->createdBy;
    }

    function getIp() {
        return $this->ip;
    }

    function getCreatedDate() {
        return $this->createdDate;
    }

    function getModifiedDate() {
        return $this->modifiedDate;
    }

    function getCountryId() {
        return $this->countryId;
    }

    function getCityId() {
        return $this->cityId;
    }

    function getCountryName() {
        return $this->countryName;
    }

    function getStateName() {
        return $this->stateName;
    }

    function getCityName() {
        return $this->cityName;
    }

    function getVenue() {
        return $this->venue;
    }

    function getImage() {
        return $this->image;
    }

    function getStartTime() {
        return $this->startTime;
    }

    function getEndTime() {
        return $this->endTime;
    }

    function getEventOrganiser() {
        return $this->eventOrganiser;
    }

    function getModifiedBy() {
        return $this->modifiedBy;
    }

    function getOrganiserPhoto() {
        return $this->organiserPhoto;
    }

    function getSponserName() {
        return $this->sponserName;
    }

    function getSponserPhoto() {
        return $this->sponserPhoto;
    }

    function getEventCost() {
        return $this->eventCost;
    }

    function getEventMembers() {
        return $this->eventMembers;
    }

    function getStateId() {
        return $this->stateId;
    }

    function getSponserContact() {
        return $this->sponserContact;
    }

    function getOrganiserContact() {
        return $this->organiserContact;
    }

    function getEndDate() {
        return $this->endDate;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setEventName($eventName) {
        $this->eventName = $eventName;
    }

    function setEventDate($eventDate) {
        $this->eventDate = $eventDate;
    }

    function setEventDesc($eventDesc) {
        $this->eventDesc = $eventDesc;
    }

    function setIsActive($isActive) {
        $this->isActive = $isActive;
    }

    function setCreatedBy($createdBy) {
        $this->createdBy = $createdBy;
    }

    function setIp($ip) {
        $this->ip = $ip;
    }

    function setCreatedDate($createdDate) {
        $this->createdDate = $createdDate;
    }

    function setModifiedDate($modifiedDate) {
        $this->modifiedDate = $modifiedDate;
    }

    function setCountryId($countryId) {
        $this->countryId = $countryId;
    }

    function setCityId($cityId) {
        $this->cityId = $cityId;
    }

    function setCountryName($countryName) {
        $this->countryName = $countryName;
    }

    function setStateName($stateName) {
        $this->stateName = $stateName;
    }

    function setCityName($cityName) {
        $this->cityName = $cityName;
    }

    function setVenue($venue) {
        $this->venue = $venue;
    }

    function setImage($image) {
        $this->image = $image;
    }

    function setStartTime($startTime) {
        $this->startTime = $startTime;
    }

    function setEndTime($endTime) {
        $this->endTime = $endTime;
    }

    function setEventOrganiser($eventOrganiser) {
        $this->eventOrganiser = $eventOrganiser;
    }

    function setModifiedBy($modifiedBy) {
        $this->modifiedBy = $modifiedBy;
    }

    function setOrganiserPhoto($organiserPhoto) {
        $this->organiserPhoto = $organiserPhoto;
    }

    function setSponserName($sponserName) {
        $this->sponserName = $sponserName;
    }

    function setSponserPhoto($sponserPhoto) {
        $this->sponserPhoto = $sponserPhoto;
    }

    function setEventCost($eventCost) {
        $this->eventCost = $eventCost;
    }

    function setEventMembers($eventMembers) {
        $this->eventMembers = $eventMembers;
    }

    function setStateId($stateId) {
        $this->stateId = $stateId;
    }

    function setSponserContact($sponserContact) {
        $this->sponserContact = $sponserContact;
    }

    function setOrganiserContact($organiserContact) {
        $this->organiserContact = $organiserContact;
    }

    function setEndDate($endDate) {
        $this->endDate = $endDate;
    }


}
   