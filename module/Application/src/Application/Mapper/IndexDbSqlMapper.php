<?php

namespace Application\Mapper;

use Application\Model\Entity\IndexInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Stdlib\Hydrator\HydratorInterface;

class IndexDbSqlMapper implements IndexMapperInterface {

    protected $dbAdapter;
    protected $hydrator;
    protected $blogPrototype;
    protected $resultSet;

    public function __construct(
    AdapterInterface $dbAdapter, HydratorInterface $hydrator = null, IndexInterface $postPrototype = null
    ) {
        $this->dbAdapter = $dbAdapter;
        $this->hydrator = $hydrator;
        $this->postPrototype = $postPrototype;
        $this->resultSet = new ResultSet();
    }

    public function delete(IndexInterface $commonObject) {
        
    }

    public function find($id) {
        
    }

    public function findAll() {
        
    }

    public function save(IndexInterface $commonObject) {
        
    }

    public function getGroomData() {
        
        $GroomData = $this->dbAdapter->query("select tbl_user.id as uid,tbl_city.*,tbl_user_info.profile_photo,tbl_user_info.full_name,tbl_user_info.age,tbl_user_info.height,tbl_user_info.city,tbl_user_info.address,tbl_user_info.about_yourself_partner_family,tbl_family_info.father_name,tbl_profession.profession from tbl_user 
		 INNER JOIN tbl_user_info on tbl_user_info.user_id=tbl_user.id
		 INNER JOIN tbl_city on tbl_user_info.city=tbl_city.id
		 INNER JOIN tbl_family_info on tbl_user.id=tbl_family_info.user_id
         INNER JOIN tbl_profession on tbl_user_info.profession=tbl_profession.id
         INNER JOIN tbl_user_roles on tbl_user_info.user_id=tbl_user_roles.user_id	
		 		 
		 where tbl_user_roles.IsMember='1'  ORDER BY tbl_user.id DESC", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
    
        return $GroomData;
    }
    
    public function getExecutiveMember() {
      
        $ExecMembers = $this->dbAdapter->query("select tbl_communities.category_name,tbl_user_info.user_id as uid,tbl_user.id,tbl_city.*,tbl_user_info.profile_photo,tbl_user_info.full_name,tbl_user_info.age,tbl_user_info.height,tbl_user_info.city,tbl_user_info.address,tbl_user_info.about_yourself_partner_family,tbl_family_info.father_name,tbl_profession.profession from tbl_user 
		 INNER JOIN tbl_user_info on tbl_user_info.user_id=tbl_user.id
		 INNER JOIN tbl_city on tbl_user_info.city=tbl_city.id
		 INNER JOIN tbl_family_info on tbl_user.id=tbl_family_info.user_id
         INNER JOIN tbl_profession on tbl_user_info.profession=tbl_profession.id		 
		 INNER JOIN tbl_user_roles on tbl_user_info.user_id=tbl_user_roles.user_id
		 INNER JOIN tbl_communities on tbl_user_info.comm_mem_id = tbl_communities.id		 
		 where tbl_user_roles.IsExecutive='1'  ORDER BY tbl_user.id DESC", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        return $ExecMembers;
        }
    
    public function getUpcomingEvents(){
         $date = date('Y-m-d h:i:s');
        $Upcoming_events = $this->dbAdapter->query("select * from tbl_upcoming_events where (event_date>'" . $date . "' && IsActive=1) ORDER BY id DESC", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        return $Upcoming_events;
        
    }

}
