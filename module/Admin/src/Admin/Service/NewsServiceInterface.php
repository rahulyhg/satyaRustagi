<?php

namespace Admin\Service;

interface NewsServiceInterface {
    
    
    
    public function getAmmir();
    
    //added by amir
    public function test();
    
    //News
    
    public function getNewsList();    

    public function getAllNewscategory();
    
    public function saveNews($newsObject);
    
    public function getNews($id);
    
    public function viewByNewsId($table, $id);
    
    //News category
    
    public function SaveNewscategory($newsCategoryObject);
    
    public function getNewscategoryList();
    
    public function getNewscategory($id);
    
    public function viewByNewscategoryId($table, $id);
    
    public function getNewscategoryRadioList($status);
    
    public function newscategorySearch($data);
    
    public function performSearchNewscategory($field);
    
    
    
    
    
    
}
