<?php

namespace Admin\Mapper;

interface AdminMapperInterface {

   public function getAmmir();
   
   public function getAmmirById($id);
   
   public function getEducationFieldList();
   
   public function changeStatus($table, $ids, $data);
   
   public function changeStatusAll($table, $ids, $data);
   
   public function delete($table, $id);
   
   public function deleteMultiple($table, $ids);
}
