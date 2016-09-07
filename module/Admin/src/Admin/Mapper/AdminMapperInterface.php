<?php

namespace Admin\Mapper;

interface AdminMapperInterface {

   public function getAmmir();
   
   public function getAmmirById($id);
   
   public function saveEducationField($educationfieldEntity);
   
   public function getEducationFieldList($status);
   
   public function getEducationField($id);
   
   public function getEducationFieldRadioList($status);
   
   public function changeStatus($table, $ids, $data);
   
   public function changeStatusAll($table, $ids, $data);
   
   public function delete($table, $id);
   
   public function deleteMultiple($table, $ids);
   
   public function viewById($table, $id);
   
   public function performSearchEducationField($field);
}
