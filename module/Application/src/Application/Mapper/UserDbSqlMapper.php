<?php

namespace Application\Mapper;

use Application\Form\Entity\SingUpFormInterface;
use Application\Model\Entity\Family;
use Application\Model\Entity\Post;
use Application\Model\Entity\User;
use Application\Model\Entity\UserInfo;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Predicate\Predicate;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Update;
use Zend\Debug\Debug;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Stdlib\Hydrator\ClassMethods;

class UserDbSqlMapper implements UserMapperInterface {

    protected $dbAdapter;
    protected $resultSet;
    protected $hydrator;

    public function __construct(AdapterInterface $dbAdapter) {

        $this->dbAdapter = $dbAdapter;
        $this->resultSet = new ResultSet();
        $this->hydrator = new ClassMethods();
    }

    public function getUserById($id) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select('tbl_user');
        $select->where(array('id = ?' => $id));

        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), new User());
        }
    }

    public function getUserInfoById($id, $columns = false) {

        $columns = ($columns == false) ? array('*') : $columns;

        $sql = new Sql($this->dbAdapter);
        $select = $sql->select('tbl_user_info');
        $select->columns($columns);
        $select->where(array('user_id = ?' => $id));

        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {

            return $this->hydrator->hydrate($result->current(), new UserInfo());
        }
    }

    public function userSummaryById($id) {
//        $sql = new Sql($this->dbAdapter);
//        $select = $sql->select('tbl_user_info');
//        $select->join('tbl_family_info', 'tbl_family_info.user_id=tbl_user_info.user_id');
//        $select->where(array('tbl_user_info.user_id = ?' => $id));
//
//        $stmt = $sql->prepareStatementForSqlObject($select);
//        $result = $stmt->execute();
        $statement = $this->dbAdapter->query("SELECT tui.*, tu.email, tu.mobile_no, tp.profession FROM tbl_user_info as tui
               LEFT JOIN tbl_user as tu ON tui.user_id=tu.id
               LEFT JOIN tbl_profession as tp ON tui.profession=tp.id
               WHERE tui.user_id=:user_id");
        $parameters = array(
            'user_id' => $id
        );
        $result = $statement->execute($parameters);
//        Debug::dump($result->current());
//        exit;
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {

            //Debug::dump($result->current());
            //exit;
            $userInfo = $this->hydrator->hydrate($result->current(), new UserInfo());
            $user = $this->hydrator->hydrate($result->current(), new User());
            //$c = (object)array_merge((array)$userInfo, (array)$user);
            return (object) array('userInfo' => $userInfo, 'user' => $user, 'profilePic' => $this->getProfilePic($id));
        }
    }

    public function getProfilePic($id) {
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_user_gallery 
               WHERE user_id=$id AND profile_pic=1 ORDER BY id DESC");

        $result = $statement->execute();
        return $result->current();
    }

    public function getUserAboutById($id) {

        $statement = $this->dbAdapter->query("SELECT id, about_yourself_partner_family as about_me FROM tbl_user_info WHERE user_id=:user_id");
        $parameters = array(
            'user_id' => $id
        );
        $result = $statement->execute($parameters);

        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {

            return $this->hydrator->hydrate($result->current(), new UserInfo());
        }
    }

    public function saveUserAbout($userAboutData) {

        $userData = $this->hydrator->extract($userAboutData);
        $userData = array_filter((array) $userData, function ($val) {
            return !is_null($val);
        });

        $userData['about_yourself_partner_family'] = $userData['about_me'];
        unset($userData['about_me']);

        $sql = new Sql($this->dbAdapter);
        $action = new Update('tbl_user_info');
        $action->set($userData);
        $action->where(array('id = ?' => $userData['id']));
        $stmt = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();
    }

    public function getUserPersonalDetailById($id) {

        $sql = new Sql($this->dbAdapter);
        $select = $sql->select('tbl_user_info');
        $select->where(array('user_id = ?' => $id));

        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {

            //return $result->current();
            return $this->hydrator->hydrate($result->current(), new UserInfo());
        }
    }

    public function educationDetailById($id) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select('tbl_user_info');
        $select->where(array('user_id = ?' => $id));

        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {

            //return $result->current();
            return $this->hydrator->hydrate($result->current(), new UserInfo());
        }
    }

    public function getUserInfoDetail($id) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select('tbl_user_info');
        $select->where(array('user_id = ?' => $id));

        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {

            return $this->hydrator->hydrate($result->current(), new UserInfo());
        }
    }

    public function checkAlreadyExist($fieldName, $value) {
        $sql = "select " . $fieldName . " from tbl_user where " . $fieldName . " like '" . $value . "%'";
        $data = $this->dbAdapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        return $data->count();
    }

    public function ProfileBar($user_id) {
        //var_dump($user_id);
        //exit;
        $data = $this->dbAdapter->query("select * from tbl_user_info as tui inner join tbl_family_info as tfi on 
            tui.user_id = tfi.user_id where tui.user_id=$user_id", Adapter::QUERY_MODE_EXECUTE)->toArray();

        $totalfields = count($data[0]);

        $arr = array('id', "ref_no", "user_id", "user_type_id", "comm_mem_id", "comm_mem_status", "ip", "created_date", "modified_date");
        $c = 0;
        foreach ($data[0] as $key => $value) {
            if (in_array($key, $arr))
                continue;
            if (!empty($value))
                $c++;
            // echo $value."<br>";
        }

        $percentage = ceil(($c / $totalfields) * 100);
        //$percentage=50;

        return $percentage; //array($percentage, $this->profilescript(ceil($percentage)));
    }

    public function getMemberInfoById($id) {
        
    }

    public function getRegisteredUserByActivationCode($id, $activationCode) {
        
    }

    public function getRegisteredUserById($id) {
        
    }

    public function getUserCareerById($id) {
        
    }

    public function getUserProfessionById($id, $columns = false) {

        $columns = ($columns == false) ? array('*') : $columns;

        $sql = new Sql($this->dbAdapter);
        $select = $sql->select('tbl_user_info');
        $select->columns($columns);
        $select->where(array('user_id = ?' => $id));

        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {

            return $this->hydrator->hydrate($result->current(), new UserInfo());
        }
    }

    public function getUserEducationAndCareerDetailById($id, $columns = false) {
        $columns = ($columns == false) ? array('*') : $columns;

        $sql = new Sql($this->dbAdapter);
        $select = $sql->select('tbl_user_info');
        $select->columns($columns);
        $select->where(array('user_id = ?' => $id));

        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {

            return $this->hydrator->hydrate($result->current(), new UserInfo());
        }
    }

    public function saveUserEducationAndCareerDetail($professionDetailsData) {

        //Debug::dump($professionDetailsData);
        //exit;
        $userData = $this->hydrator->extract($professionDetailsData);
        $userData = array_filter((array) $userData, function ($val) {
            return !is_null($val);
        });

        unset($userData['created_date']);
        //Debug::dump($userData);
        //exit;
        $sql = new Sql($this->dbAdapter);
        $action = new Update('tbl_user_info');
        $action->set($userData);
        $action->where(array('id = ?' => $userData['id']));
        $stmt = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();
    }

    public function getUserEducationById($id) {
        
    }

    public function getUserMatrimonialById($id) {
        
    }

    public function removeUser($id) {
        
    }

    public function save(SingUpFormInterface $userObject) {

        $sql = new Sql($this->dbAdapter);

        $userData['user_type_id'] = $userObject->getUserTypeId();
        $userData['username'] = $userObject->getUsername();
        $userData['password'] = $userObject->getPassword();
        $userData['email'] = $userObject->getEmail();
        $userData['mobile_no'] = $userObject->getMobileNo();
        $userData['activation_key'] = md5($userObject->getEmail());
        $userData['ip'] = $userObject->getIp();
        $userData['created_date'] = $userObject->getCreatedDate();
        $userData['Modified_Date'] = $userObject->getModifiedDate();

        $action = new Insert('tbl_user');
        $action->values($userData);
        $stmt = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();
        if ($result instanceof ResultInterface) {
            if ($newId = $result->getGeneratedValue()) {
                $userObject->setUserId($newId);
                $ref_no = $this->createReferenceNumber($userObject->getFullName(), $userObject->getUserId());
                $userObject->setRefNo($ref_no);
                $action = new Update('tbl_user');
                $action->set(array('ref_no' => $ref_no));
                $action->where(array('id = ?' => $userObject->getUserId()));
                $stmt = $sql->prepareStatementForSqlObject($action);
                $result = $stmt->execute();
            }
            if ($userObject->getUserId()) {
                $userInfoData['ref_no'] = $userObject->getRefNo();
                $userInfoData['user_id'] = $userObject->getUserId();
                $userInfoData['user_type_id'] = $userObject->getUserTypeId();
                $userInfoData['ip'] = $userObject->getIp();
                $userInfoData['full_name'] = $userObject->getFullName();
                $userInfoData['name_title_user'] = $userObject->getNameTitleUser();
                $userInfoData['gender'] = $userObject->getGender();
                $userInfoData['address'] = $userObject->getAddress();
                $userInfoData['country'] = $userObject->getCountry();
                $userInfoData['state'] = $userObject->getState();
                $userInfoData['city'] = $userObject->getCity();
                $userInfoData['branch_ids'] = $userObject->getRustagiBranch();
                $userInfoData['branch_ids_other'] = $userObject->getRustagiBranchOther();
                $userInfoData['native_place'] = $userObject->getNativePlace();
                $userInfoData['gothra_gothram'] = $userObject->getGothraGothram();
                $userInfoData['gothra_gothram_other'] = $userObject->getGothraGothramOther();
                $userInfoData['created_date'] = $userObject->getCreatedDate();
                $userInfoData['modified_date'] = $userObject->getModifiedDate();

                $action = new Insert('tbl_user_info');
                $action->values($userInfoData);
                $stmt = $sql->prepareStatementForSqlObject($action);
                $result = $stmt->execute();
                if ($result instanceof ResultInterface) {

                    $familyInfoData["user_id"] = $userObject->getUserId();
                    $familyInfoData["relation_id"] = (int) 1;
                    $familyInfoData["title"] = $userObject->getNameTitleUser();
                    $familyInfoData["name"] = $userObject->getFatherName();
                    $familyInfoData['ip'] = $userObject->getIp();
                    $familyInfoData['created_date'] = $userObject->getCreatedDate();
                    $familyInfoData['modified_date'] = $userObject->getModifiedDate();

                    $action = new Insert('tbl_family_relation_info');
                    $action->values($familyInfoData);
                    $stmt = $sql->prepareStatementForSqlObject($action);
                    $result = $stmt->execute();
                }
            }
            return $userObject;
        }
    }

    public function saveUser(SingUpFormInterface $userObject) {

        $sql = new Sql($this->dbAdapter);

        $userData['user_type_id'] = $userObject->getUserTypeId();
        $userData['username'] = $userObject->getUsername();
        $userData['password'] = $userObject->getPassword();
        $userData['email'] = $userObject->getEmail();
        $userData['mobile_no'] = $userObject->getMobileNo();
        $userData['activation_key'] = md5($userObject->getEmail());
        $userData['ip'] = $userObject->getIp();
        $userData['created_date'] = $userObject->getCreatedDate();
        $userData['Modified_Date'] = $userObject->getModifiedDate();

        $action = new Insert('tbl_user');
        $action->values($userData);
        $stmt = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();
        if ($result instanceof ResultInterface) {
            if ($newId = $result->getGeneratedValue()) {
                $userObject->setUserId($newId);
                $ref_no = $this->createReferenceNumber($userObject->getFullName(), $userObject->getUserId());
                $userObject->setRefNo($ref_no);
                $action = new Update('tbl_user');
                $action->set(array('ref_no' => $ref_no));
                $action->where(array('id = ?' => $userObject->getUserId()));
                $stmt = $sql->prepareStatementForSqlObject($action);
                $result = $stmt->execute();
            }
            if ($userObject->getUserId()) {
                $userInfoData['ref_no'] = $userObject->getRefNo();
                $userInfoData['user_id'] = $userObject->getUserId();
                $userInfoData['user_type_id'] = $userObject->getUserTypeId();
                $userInfoData['ip'] = $userObject->getIp();
                $userInfoData['full_name'] = $userObject->getFullName();
                $userInfoData['name_title_user'] = $userObject->getNameTitleUser();
                $userInfoData['gender'] = $userObject->getGender();
                $userInfoData['address'] = $userObject->getAddress();
                $userInfoData['country'] = $userObject->getCountry();
                $userInfoData['state'] = $userObject->getState();
                $userInfoData['city'] = $userObject->getCity();
                $userInfoData['branch_ids'] = $userObject->getRustagiBranch();
                $userInfoData['branch_ids_other'] = $userObject->getRustagiBranchOther();
                $userInfoData['native_place'] = $userObject->getNativePlace();
                $userInfoData['gothra_gothram'] = $userObject->getGothraGothram();
                $userInfoData['gothra_gothram_other'] = $userObject->getGothraGothramOther();
                $userInfoData['created_date'] = $userObject->getCreatedDate();
                $userInfoData['modified_date'] = $userObject->getModifiedDate();

                $action = new Insert('tbl_user_info');
                $action->values($userInfoData);
                $stmt = $sql->prepareStatementForSqlObject($action);
                $result = $stmt->execute();
                if ($result instanceof ResultInterface) {

                    $familyInfoData["user_id"] = $userObject->getUserId();
                    $familyInfoData["relation_id"] = (int) 1;
                    $familyInfoData["title"] = $userObject->getNameTitleUser();
                    $familyInfoData["name"] = $userObject->getFatherName();
                    $familyInfoData['ip'] = $userObject->getIp();
                    $familyInfoData['created_date'] = $userObject->getCreatedDate();
                    $familyInfoData['modified_date'] = $userObject->getModifiedDate();

                    $action = new Insert('tbl_family_relation_info');
                    $action->values($familyInfoData);
                    $stmt = $sql->prepareStatementForSqlObject($action);
                    $result = $stmt->execute();
                }
            }
            return $userObject;
        }
    }

    public function saveUserSignUp(SingUpFormInterface $userObject) {

        $sql = new Sql($this->dbAdapter);
        $userObject->setActivationKey(md5($userObject->getEmail()));
        $userData['user_type_id'] = $userObject->getUserTypeId();
        $userData['username'] = $userObject->getUsername();
        $userData['password'] = $userObject->getPassword();
        $userData['email'] = $userObject->getEmail();
        $userData['mobile_no'] = $userObject->getMobileNo();
        $userData['activation_key'] = $userObject->getActivationKey();
        $userData['ip'] = $userObject->getIp();
        $userData['created_date'] = $userObject->getCreatedDate();
        $userData['Modified_Date'] = $userObject->getModifiedDate();

        $action = new Insert('tbl_user');
        $action->values($userData);
        $stmt = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();
        if ($result instanceof ResultInterface) {
            if ($newId = $result->getGeneratedValue()) {
                $userObject->setUserId($newId);
                $ref_no = $this->createReferenceNumber($userObject->getFullName(), $userObject->getUserId());
                $userObject->setRefNo($ref_no);
                $action = new Update('tbl_user');
                $action->set(array('ref_no' => $ref_no));
                $action->where(array('id = ?' => $userObject->getUserId()));
                $stmt = $sql->prepareStatementForSqlObject($action);
                $result = $stmt->execute();
            }
            if ($userObject->getUserId()) {
                $userInfoData['ref_no'] = $userObject->getRefNo();
                $userInfoData['user_id'] = $userObject->getUserId();
                $userInfoData['user_type_id'] = $userObject->getUserTypeId();
                $userInfoData['ip'] = $userObject->getIp();
                $userInfoData['full_name'] = $userObject->getFullName();
                $userInfoData['name_title_user'] = $userObject->getNameTitleUser();
                $userInfoData['gender'] = $userObject->getGender();
                $userInfoData['address'] = $userObject->getAddress();
                $userInfoData['country'] = $userObject->getCountry();
                $userInfoData['state'] = $userObject->getState();
                $userInfoData['city'] = $userObject->getCity();
                $userInfoData['branch_ids'] = $userObject->getRustagiBranch();
                $userInfoData['branch_ids_other'] = $userObject->getRustagiBranchOther();
                $userInfoData['native_place'] = $userObject->getNativePlace();
                $userInfoData['gothra_gothram'] = $userObject->getGothraGothram();
                $userInfoData['gothra_gothram_other'] = $userObject->getGothraGothramOther();
                $userInfoData['created_date'] = $userObject->getCreatedDate();
                $userInfoData['modified_date'] = $userObject->getModifiedDate();

                $action = new Insert('tbl_user_info');
                $action->values($userInfoData);
                $stmt = $sql->prepareStatementForSqlObject($action);
                $result = $stmt->execute();
                if ($result instanceof ResultInterface) {

                    $selfRelationData['user_id'] = $userObject->getUserId();
                    $selfRelationData['gender'] = $userObject->getGender();
                    $action = new Insert('tbl_family_relation');
                    $action->values($selfRelationData);
                    $stmt = $sql->prepareStatementForSqlObject($action);
                    $result = $stmt->execute();

                    $profileId = $this->createFamilyProfile($userObject->getFatherName(), $userObject->getNameTitleUser(), 'Alive', '', '', 'Male');

                    $this->saveRelation($userObject->getUserId(), $profileId, 'f');

//                    $familyInfoData["user_id"] = $userObject->getUserId();
//                    $familyInfoData["relation_id"] = (int) 1;
//                    $familyInfoData["title"] = $userObject->getNameTitleUser();
//                    $familyInfoData["name"] = $userObject->getFatherName();
//                    $familyInfoData['ip'] = $userObject->getIp();
//                    $familyInfoData['created_date'] = $userObject->getCreatedDate();
//                    $familyInfoData['modified_date'] = $userObject->getModifiedDate();
//
//                    $action = new Insert('tbl_family_relation_info');
//                    $action->values($familyInfoData);
//                    $stmt = $sql->prepareStatementForSqlObject($action);
//                    $result = $stmt->execute();
                }
            }
            return $userObject;
        }
    }

    public function saveUserPersonalDetails($personalDetailsObject) {

        $userData = $this->hydrator->extract($personalDetailsObject);
        $userData = array_filter((array) $userData, function ($val) {
            return !is_null($val);
        });
        //Debug::dump($userData);
        //exit;
        //$remote = new RemoteAddress;
        //$this->ip = $remote->getIpAddress();
        $sql = new Sql($this->dbAdapter);



        $action = new Update('tbl_user_info');
        $action->set($userData);
        $action->where(array('id = ?' => $userData['id']));
        $stmt = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();
    }

    public function saveUserEducationDetails($educationDetailsData) {

        $userData = $this->hydrator->extract($educationDetailsData);
        unset($userData['created_date']);
        //Debug::dump($userData);
        //exit;
        $sql = new Sql($this->dbAdapter);
        $action = new Update('tbl_user_info');
        $action->set($userData);
        $action->where(array('id = ?' => $userData['id']));
        $stmt = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();
    }

    public function saveUserProfessionDetails($professionDetailsData) {

        $userData = $this->hydrator->extract($professionDetailsData);
        unset($userData['created_date']);
        //Debug::dump($userData);
        //exit;
        $sql = new Sql($this->dbAdapter);
        $action = new Update('tbl_user_info');
        $action->set($userData);
        $action->where(array('id = ?' => $userData['id']));
        $stmt = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();
    }

    public function saveAcitivationSmsCode($userId, $number, $code, $time) {
        $sql = new Sql($this->dbAdapter);

        $userData['user_id'] = $userId;
        $userData['mobile'] = $number;
        $userData['code'] = $code;
        $userData['time'] = $time;

        $action = new Insert('tbl_mobile');
        $action->values($userData);
        $stmt = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();
        if ($result instanceof ResultInterface) {
            if ($newId = $result->getGeneratedValue()) {
                // When a value has been generated, set it on the object
                return $newId;
            }
        }
    }

    public function saveUserInfo($userInfoObject) {
        $userInfoData = $this->hydrator->extract($userInfoObject);
        //unset($userInfoData['id']); // Neither Insert nor Update needs the ID in the array

        Debug::dump($userInfoData);
        exit;
        $action = new Insert('tbl_user_info');
        $action->values($userInfoData);

        $sql = new Sql($this->dbAdapter);
        $stmt = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();

        if ($result instanceof ResultInterface) {
            if ($newId = $result->getGeneratedValue()) {
                // When a value has been generated, set it on the object
                $userInfoObject->setId($newId);
            }

            return $userInfoObject;
        }
    }

    public function saveUserCareer($careerInfo) {
        
    }

    public function saveUserEducation($educationInfo) {
        
    }

    public function saveUserMatrimonial($matrimonialInfo) {
        
    }

    public function createReferenceNumber($fullName, $id) {
        $dateYear = date('y');
        if ($dateYear > 26) {
            $dateYear = $dateYear - 26;
            $dateYear = 64 + $dateYear;
            $dateYear = chr($dateYear);
            $dateYear = "A" . $dateYear;
        } else {
            $dateYear = 64 + $dateYear;
            $dateYear = chr($dateYear);
        }
        $full_nameArray = explode(' ', $fullName);
        if (count($full_nameArray) > 1) {
            $first = strtoupper(substr($full_nameArray[0], 0, 1));
            $last = strtoupper(substr($full_nameArray[1], 0, 1));
            $referenceNo = $dateYear . $first . $last . $id;
        } else {
            $first = strtoupper(substr($full_nameArray[0], 0, 2));
            $referenceNo = $dateYear . $first . $id;
        }
        return $referenceNo;
    }

    public function getUserPostById($user_id) {

        $statement = $this->dbAdapter->query("SELECT * FROM tbl_post WHERE id=:id");
        $parameters = array(
            'id' => $user_id
        );
        $result = $statement->execute($parameters);

        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {

            return $this->hydrator->hydrate($result->current(), new Post());
        }
    }

    public function saveUserPost($userPostData) {
        //$postDataArray = $this->hydrator->extract($userPostData);
//        $postDataArray = array_filter((array) $postDataArray, function ($val) {
//            return !is_null($val);
//        });
//        $userData['about_yourself_partner_family']=$userData['about_me'];
        //unset($userData['about_me']);
        //Debug::dump($userPostData);
        //exit;
        if ($userPostData['id']) {
            $postData = array();

            $postData['post_category'] = $userPostData['post_category'];
            $postData['title'] = $userPostData['title'];
            $postData['description'] = $userPostData['description'];
            if (!empty($userPostData['image']['name'])) {
                $postData['image'] = $userPostData['image']['name'];
            }
            //$postData['modified_date'] = $userPostData['modified_date'];
            //Debug::dump($userPostData);
            //exit;


            $action = new Update('tbl_post');
            $action->set($postData);
            $action->where(array('id = ?' => $userPostData['id']));
            $sql = new Sql($this->dbAdapter);
            $stmt = $sql->prepareStatementForSqlObject($action);
            $result = $stmt->execute();
        } else {
            $postData = array();
            $postData['user_id'] = $userPostData['user_id'];
            $postData['post_category'] = $userPostData['post_category'];
            $postData['title'] = $userPostData['title'];
            $postData['description'] = $userPostData['description'];
            if (!empty($userPostData['image']['name'])) {
                $postData['image'] = $userPostData['image']['name'];
            }
          
            //Debug::dump($userPostData);
            //exit;

            $action = new Insert('tbl_post');
            $action->values($postData);

            $sql = new Sql($this->dbAdapter);
            $stmt = $sql->prepareStatementForSqlObject($action);
            $result = $stmt->execute();
        }
    }

    function my_array_search($array, $key, $value) {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, $this->my_array_search($subarray, $key, $value));
            }
        }

        return $results;
    }

    public function getFamilyInfoById($user_id) {
//        $allParent=$this->getFirstParent($user_id);
//        $myChild = $this->getAllChild($user_id);
//        $allChild = $this->getAllChild($user_id);
//        $myFatherId=$allParent[0];
//        $myGrandFatherId=$allParent[1];
//        $brotherArray=$this->my_array_search($this->getAllChild($myFatherId), 'father_id', $myFatherId);
//        $keyMy = array_search($user_id, array_column($brotherArray, 'user_id'));
//        unset($brotherArray[$keyMy]);
//        //array_reduce($a, 'array_merge', array());
//        $myBrotherData=$this->my_array_search(array_values($brotherArray), 'gender', 'Male');
//        $mySisterData=$this->my_array_search(array_values($brotherArray), 'gender', 'Female');
        //$mybrotherId=$mybrotherData['user_id'];
        //Debug::dump($this->my_array_search($child, 'father_id', '38'));
        //Debug::dump($allChild);
        $family = new Family();
        $familyInfo = array();


        $statement = $this->dbAdapter->query("SELECT tui.name_title_user, tui.full_name, 
            tui.dob,tui.dod, tui.live_status,tui.ref_no,
            tui.profile_photo, tui.gender, tui.family_values_status, tui.user_id as user_id,
            tfr.user_id as user_id_rel, tfr.father_id, tfr.mother_id, tfr.wife_id, tfr.husband_id  FROM tbl_user_info as tui LEFT JOIN tbl_family_relation as tfr 
                ON tui.user_id=tfr.user_id WHERE tui.user_id=:user_id");
        $parameters = array(
            'user_id' => $user_id
        );
        $statementGallery = $this->dbAdapter->query("SELECT * FROM tbl_user_gallery WHERE user_id=:user_id");
        $parameters = array(
            'user_id' => $user_id
        );
        $result = $statement->execute($parameters);
        $userInfo = $result->current();
        $family->setUserId($userInfo['user_id']);
        $family->setFamilyValues($userInfo['family_values_status']);

// father
        $parameters = array(
            'user_id' => $userInfo['father_id']
        );
        $result = $statement->execute($parameters);
        $fatherInfo = $result->current();
        $familyInfo['father_id'] = $fatherInfo['user_id'];
        $familyInfo['father_ref_no'] = $fatherInfo['ref_no'];
        $familyInfo['name_title_father'] = $fatherInfo['name_title_user'];
        $familyInfo['father_name'] = $fatherInfo['full_name'];
        $familyInfo['father_dob'] = $fatherInfo['dob'];
        $familyInfo['father_status'] = $fatherInfo['live_status'];
        $familyInfo['father_photo'] = $fatherInfo['profile_photo'];
        $parametersGallery = array(
            'user_id' => $fatherInfo['user_id']
        );
        $resultGallery = $statementGallery->execute($parametersGallery);
        $GalleryInfo['father'] = $resultGallery->current();

        $family->setFatherId($fatherInfo['user_id']);
        $family->setNameTitleFather($fatherInfo['name_title_user']);
        $family->setFatherName($fatherInfo['full_name']);
        $family->setFatherDob(date('d-m-Y', strtotime($fatherInfo['dob'])));
        $family->setFatherDod(date('d-m-Y', strtotime($fatherInfo['dod'])));
        $family->setFatherStatus($fatherInfo['live_status']);
        $family->setFatherPhoto($fatherInfo['profile_photo']);


// mother
        $parameters = array(
            'user_id' => $userInfo['mother_id']
        );
        $result = $statement->execute($parameters);
        $motherInfo = $result->current();
        $familyInfo['mother_id'] = $motherInfo['user_id'];
        $familyInfo['mother_ref_no'] = $motherInfo['ref_no'];
        $familyInfo['name_title_mother'] = $motherInfo['name_title_user'];
        $familyInfo['mother_name'] = $motherInfo['full_name'];
        $familyInfo['mother_dob'] = $motherInfo['dob'];
        $familyInfo['mother_status'] = $motherInfo['live_status'];
        $familyInfo['mother_photo'] = $motherInfo['profile_photo'];
        $parametersGallery = array(
            'user_id' => $motherInfo['user_id']
        );
        $resultGallery = $statementGallery->execute($parametersGallery);
        $GalleryInfo['mother'] = $resultGallery->current();

        $family->setMotherId($motherInfo['user_id']);
        $family->setNameTitleMother($motherInfo['name_title_user']);
        $family->setMotherName($motherInfo['full_name']);
        $family->setMotherDob(date('d-m-Y', strtotime($motherInfo['dob'])));
        $family->setMotherDod(date('d-m-Y', strtotime($motherInfo['dod'])));
        $family->setMotherStatus($motherInfo['live_status']);
        $family->setMotherPhoto($motherInfo['profile_photo']);


// spouse wife
        if ($userInfo['gender'] === "Male") {
            $parameters = array(
                'user_id' => $userInfo['wife_id']
            );
            $result = $statement->execute($parameters);
            $wifeInfo = $result->current();
            $familyInfo['spouse_id'] = $wifeInfo['user_id'];
            $familyInfo['spouse_ref_no'] = $wifeInfo['ref_no'];
            $familyInfo['name_title_spouse'] = $wifeInfo['name_title_user'];
            $familyInfo['spouse_name'] = $wifeInfo['full_name'];
            $familyInfo['spouse_dob'] = $wifeInfo['dob'];
            $familyInfo['spouse_status'] = $wifeInfo['live_status'];
            $familyInfo['spouse_photo'] = $wifeInfo['profile_photo'];
            $parametersGallery = array(
                'user_id' => $wifeInfo['user_id']
            );
            $resultGallery = $statementGallery->execute($parametersGallery);
            $GalleryInfo['spouse'] = $resultGallery->current();

            $family->setSpouseId($wifeInfo['user_id']);
            $family->setNameTitleSpouse($wifeInfo['name_title_user']);
            $family->setSpouseName($wifeInfo['full_name']);
            $family->setSpouseDob(date('d-m-Y', strtotime($wifeInfo['dob'])));
            $family->setSpouseDiedOn(date('d-m-Y', strtotime($wifeInfo['dod'])));
            $family->setSpouseStatus($wifeInfo['live_status']);
            $family->setSpousePhoto($wifeInfo['profile_photo']);
        }
// spouse husband
        if ($userInfo['gender'] === "Female") {
            $parameters = array(
                'user_id' => $userInfo['husband_id']
            );
            $result = $statement->execute($parameters);
            $husbandInfo = $result->current();
            $familyInfo['spouse_id'] = $husbandInfo['user_id'];
            $familyInfo['spouse_ref_no'] = $husbandInfo['ref_no'];
            $familyInfo['name_title_spouse'] = $husbandInfo['name_title_user'];
            $familyInfo['spouse_name'] = $husbandInfo['full_name'];
            $familyInfo['spouse_dob'] = $husbandInfo['dob'];
            $familyInfo['spouse_status'] = $husbandInfo['live_status'];
            $familyInfo['spouse_photo'] = $husbandInfo['profile_photo'];
            $parametersGallery = array(
                'user_id' => $husbandInfo['user_id']
            );
            $resultGallery = $statementGallery->execute($parametersGallery);
            $GalleryInfo['spouse'] = $resultGallery->current();

            $family->setSpouseId($husbandInfo['user_id']);
            $family->setNameTitleSpouse($husbandInfo['name_title_user']);
            $family->setSpouseName($husbandInfo['full_name']);
            $family->setSpouseDob(date('d-m-Y', strtotime($husbandInfo['dob'])));
            $family->setSpouseDiedOn(date('d-m-Y', strtotime($wifeInfo['dod'])));
            $family->setSpouseStatus($husbandInfo['live_status']);
            $family->setSpousePhoto($husbandInfo['profile_photo']);
        }

// grand father
        $parameters = array(
            'user_id' => $fatherInfo['father_id']
        );
        $result = $statement->execute($parameters);
        $grandFatherInfo = $result->current();
        $familyInfo['grand_father_id'] = $grandFatherInfo['user_id'];
        $familyInfo['grand_father_ref_no'] = $grandFatherInfo['ref_no'];
        $familyInfo['name_title_grand_father'] = $grandFatherInfo['name_title_user'];
        $familyInfo['grand_father_name'] = $grandFatherInfo['full_name'];
        $familyInfo['grand_father_dob'] = $grandFatherInfo['dob'];
        $familyInfo['grand_father_status'] = $grandFatherInfo['live_status'];
        $familyInfo['grand_father_photo'] = $grandFatherInfo['profile_photo'];
        $parametersGallery = array(
            'user_id' => $grandFatherInfo['user_id']
        );
        $resultGallery = $statementGallery->execute($parametersGallery);
        $GalleryInfo['grand_father'] = $resultGallery->current();

        $family->setGrandFatherId($grandFatherInfo['user_id']);
        $family->setNameTitleGrandFather($grandFatherInfo['name_title_user']);
        $family->setGrandFatherName($grandFatherInfo['full_name']);
        $family->setGrandFatherDob(date('d-m-Y', strtotime($grandFatherInfo['dob'])));
        $family->setGrandFatherDod(date('d-m-Y', strtotime($grandFatherInfo['dod'])));
        $family->setGrandFatherStatus($grandFatherInfo['live_status']);
        $family->setGrandFatherPhoto($grandFatherInfo['profile_photo']);


// grand mother
        $parameters = array(
            'user_id' => $fatherInfo['mother_id']
        );
        $result = $statement->execute($parameters);
        $grandMotherInfo = $result->current();
        $familyInfo['grand_mother_id'] = $grandMotherInfo['user_id'];
        $familyInfo['grand_mother_ref_no'] = $grandMotherInfo['ref_no'];
        $familyInfo['name_title_grand_mother'] = $grandMotherInfo['name_title_user'];
        $familyInfo['grand_mother_name'] = $grandMotherInfo['full_name'];
        $familyInfo['grand_mother_dob'] = $grandMotherInfo['dob'];
        $familyInfo['grand_mother_status'] = $grandMotherInfo['live_status'];
        $familyInfo['grand_mother_photo'] = $grandMotherInfo['profile_photo'];

        $parametersGallery = array(
            'user_id' => $grandMotherInfo['user_id']
        );
        $resultGallery = $statementGallery->execute($parametersGallery);
        $GalleryInfo['grand_mother'] = $resultGallery->current();

        $family->setGrandMotherId($grandMotherInfo['user_id']);
        $family->setNameTitleGrandMother($grandMotherInfo['name_title_user']);
        $family->setGrandMotherName($grandMotherInfo['full_name']);
        $family->setGrandMotherDob(date('d-m-Y', strtotime($grandMotherInfo['dob'])));
        $family->setGrandMotherDod(date('d-m-Y', strtotime($grandMotherInfo['dod'])));
        $family->setGrandMotherStatus($grandMotherInfo['live_status']);
        $family->setGrandMotherPhoto($grandMotherInfo['profile_photo']);

// grand grand mother
        $parameters = array(
            'user_id' => $grandFatherInfo['mother_id']
        );
        $result = $statement->execute($parameters);
        $grandGrandMotherInfo = $result->current();
        $familyInfo['grand_grand_mother_id'] = $grandGrandMotherInfo['user_id'];
        $familyInfo['grand_grand_mother_ref_no'] = $grandGrandMotherInfo['ref_no'];
        $familyInfo['name_title_grand_grand_mother'] = $grandGrandMotherInfo['name_title_user'];
        $familyInfo['grand_grand_mother_name'] = $grandGrandMotherInfo['full_name'];
        $familyInfo['grand_grand_mother_dob'] = $grandGrandMotherInfo['dob'];
        $familyInfo['grand_grand_mother_status'] = $grandGrandMotherInfo['live_status'];
        $familyInfo['grand_grand_mother_photo'] = $grandGrandMotherInfo['profile_photo'];

        $parametersGallery = array(
            'user_id' => $grandGrandMotherInfo['user_id']
        );
        $resultGallery = $statementGallery->execute($parametersGallery);
        $GalleryInfo['grand_grand_mother'] = $resultGallery->current();


        $family->setGrandGrandMotherId($grandGrandMotherInfo['user_id']);
        $family->setNameTitleGrandGrandMother($grandGrandMotherInfo['name_title_user']);
        $family->setGrandGrandMotherName($grandGrandMotherInfo['full_name']);
        $family->setGrandGrandMotherDob(date('d-m-Y', strtotime($grandGrandMotherInfo['dob'])));
        $family->setGrandGrandMotherDod(date('d-m-Y', strtotime($grandGrandMotherInfo['dod'])));
        $family->setGrandGrandMotherStatus($grandGrandMotherInfo['live_status']);
        $family->setGrandGrandMotherPhoto($grandGrandMotherInfo['profile_photo']);


// grand grand father
        $parameters = array(
            'user_id' => $grandFatherInfo['father_id']
        );
        $result = $statement->execute($parameters);
        $grandGrandFatherInfo = $result->current();
        $familyInfo['grand_grand_father_id'] = $grandGrandFatherInfo['user_id'];
        $familyInfo['grand_grand_father_ref_no'] = $grandGrandFatherInfo['ref_no'];
        $familyInfo['name_title_grand_grand_father'] = $grandGrandFatherInfo['name_title_user'];
        $familyInfo['grand_grand_father_name'] = $grandGrandFatherInfo['full_name'];
        $familyInfo['grand_grand_father_dob'] = $grandGrandFatherInfo['dob'];
        $familyInfo['grand_grand_father_status'] = $grandGrandFatherInfo['live_status'];
        $familyInfo['grand_grand_father_photo'] = $grandGrandFatherInfo['profile_photo'];

        $parametersGallery = array(
            'user_id' => $grandGrandFatherInfo['user_id']
        );
        $resultGallery = $statementGallery->execute($parametersGallery);
        $GalleryInfo['grand_grand_father'] = $resultGallery->current();


        $family->setGrandGrandFatherId($grandGrandFatherInfo['user_id']);
        $family->setNameTitleGrandGrandFather($grandGrandFatherInfo['name_title_user']);
        $family->setGrandGrandFatherName($grandGrandFatherInfo['full_name']);
        $family->setGrandGrandFatherDob(date('d-m-Y', strtotime($grandGrandFatherInfo['dob'])));
        $family->setGrandGrandFatherDod(date('d-m-Y', strtotime($grandGrandFatherInfo['dod'])));
        $family->setGrandGrandFatherStatus($grandGrandFatherInfo['live_status']);
        $family->setGrandGrandFatherPhoto($grandGrandFatherInfo['profile_photo']);

// brother

        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(array('tui' => 'tbl_user_info'));
        $select->join(array('tfr' => 'tbl_family_relation'), 'tui.user_id=tfr.user_id', array('user_id_rel' => 'user_id', 'father_id'), $select::JOIN_LEFT);
        $select->join(array('tug' => 'tbl_user_gallery'), 'tui.user_id=tug.user_id', array('image_path'), $select::JOIN_LEFT);
        $select->columns(array('name_title_user', 'brother_name' => 'full_name',
            'dob', 'live_status',
            'profile_photo', 'gender', 'family_values_status', 'ref_no', 'user_id' => 'user_id',
        ));
        $select->where(array('tfr.father_id = ?' => $userInfo['father_id']));
        $select->where(array('tui.user_id != ?' => $user_id), Predicate::OP_AND);
        $select->where(array('tui.gender = ?' => 'Male'), Predicate::OP_AND);
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        //$resultSet = new HydratingResultSet($this->hydrator, $family);

        $resultSet = $this->resultSet->initialize($result);
        $brotherData = $resultSet->toArray();
//        foreach ($brotherData as $brotherDatas) {
//            $parametersGallery = array(
//                'user_id' => $grandGrandFatherInfo['user_id']
//            );
//            $resultGallery = $statementGallery->execute($parametersGallery);
//            $GalleryInfo['brother'] = $resultGallery->current();
//        }
        //Debug::dump($brotherData);
        $family->setNumbor($resultSet->count() > 0 ? $resultSet->count() : '');

// Sister

        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(array('tui' => 'tbl_user_info'));
        $select->join(array('tfr' => 'tbl_family_relation'), 'tui.user_id=tfr.user_id', array('user_id_rel' => 'user_id', 'father_id'), $select::JOIN_LEFT);
        $select->join(array('tug' => 'tbl_user_gallery'), 'tui.user_id=tug.user_id', array('image_path'), $select::JOIN_LEFT);
        $select->columns(array('name_title_user', 'sister_name' => 'full_name',
            'dob', 'live_status',
            'profile_photo', 'gender', 'family_values_status', 'user_id' => 'user_id',
        ));
        $select->where(array('tfr.father_id = ?' => $userInfo['father_id']));
        $select->where(array('tui.user_id != ?' => $user_id), Predicate::OP_AND);
        $select->where(array('tui.gender = ?' => 'Female'), Predicate::OP_AND);

        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        //$resultSet = new HydratingResultSet($this->hydrator, $family);

        $resultSet = $this->resultSet->initialize($result);
        //Debug::dump($resultSet);
        $sisterData = $resultSet->toArray();

// Kids

        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(array('tui' => 'tbl_user_info'));
        $select->join(array('tfr' => 'tbl_family_relation'), 'tui.user_id=tfr.user_id', array('user_id_rel' => 'user_id', 'father_id'), $select::JOIN_LEFT);
        $select->join(array('tug' => 'tbl_user_gallery'), 'tui.user_id=tug.user_id', array('image_path'), $select::JOIN_LEFT);
        $select->columns(array('name_title_user', 'kids_name' => 'full_name',
            'dob', 'live_status',
            'profile_photo', 'gender', 'family_values_status', 'user_id' => 'user_id',
        ));
        $select->where(array('tfr.father_id = ?' => $user_id));

        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        //$resultSet = new HydratingResultSet($this->hydrator, $family);

        $resultSet = $this->resultSet->initialize($result);
        //Debug::dump($resultSet);
        $kidsData = $resultSet->toArray();
        //Debug::dump($kidsData);
        //$family->setNumbor($brotherData->count() > 0 ? $brotherData->count() : '');


        return (object) array('userInfo' => $userInfo,
                    'familyInfoObject' => $family,
                    'brotherData' => $brotherData,
                    'sisterData' => $sisterData,
                    'kidsData' => $kidsData,
                    'familyInfoArray' => $familyInfo,
                    'GalleryInfo' => $GalleryInfo);
        //return $family;
        //Debug::dump($fatherInfo);
        //exit;
//        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
//
//            $userInfo = $this->hydrator->hydrate($result->current(), new UserInfo());
//            $family = $this->hydrator->hydrate($result->current(), new \Application\Model\Entity\Family());
//            //$c = (object)array_merge((array)$userInfo, (array)$user);
//            return (object) array('userInfo' => $userInfo, 'family' => $family);
//
//            //return $this->hydrator->hydrate($result->current(), new \Application\Model\Entity\Family());
//            //return $result->current();
//        }
    }

    public function saveFamilyInfo($user_id, $familyData) {

        $family_id = unserialize($familyData['family_id']);
        //Debug::dump($family_id);
        //Debug::dump($familyData);
        //exit;
        $sql = new Sql($this->dbAdapter);

        if ($familyData['marital_status']) {
            $userDataMerital['marital_status'] = $familyData['marital_status'];
            $action = new Update('tbl_user_info');
            $action->set($userDataMerital);
            $action->where(array('user_id = ?' => $user_id));
            $stmt = $sql->prepareStatementForSqlObject($action);
            //Debug::dump($stmt);
            //exit;
            $result = $stmt->execute();
        }

        if ($family_id['father_id']) {

            $userData['user_id'] = $family_id['father_id'];
            $userData['name_title_user'] = $familyData['name_title_father'];
            $userData['full_name'] = $familyData['father_name'];
            $userData['live_status'] = $familyData['father_status'];
            $userData['dob'] = date('Y-m-d', strtotime($familyData['father_dob']));
            $userData['dod'] = date('Y-m-d', strtotime($familyData['father_dod']));
            //$userData['time'] = $familyData;

            $action = new Update('tbl_user_info');
            $action->set($userData);
            $action->where(array('user_id = ?' => $userData['user_id']));
            $stmt = $sql->prepareStatementForSqlObject($action);
            //Debug::dump($userData);
            //exit;
            $result = $stmt->execute();
        }

        if ($family_id['mother_id']) {

            $userData['user_id'] = $family_id['mother_id'];
            $userData['name_title_user'] = $familyData['name_title_mother'];
            $userData['full_name'] = $familyData['mother_name'];
            $userData['live_status'] = $familyData['mother_status'];
            $userData['dob'] = date('Y-m-d', strtotime($familyData['mother_dob']));
            $userData['dod'] = date('Y-m-d', strtotime($familyData['mother_dod']));
            //$userData['time'] = $familyData;

            $action = new Update('tbl_user_info');
            $action->set($userData);
            $action->where(array('user_id = ?' => $userData['user_id']));
            $stmt = $sql->prepareStatementForSqlObject($action);
            //Debug::dump($userData);
            //exit;
            $result = $stmt->execute();
        } elseif (!empty($familyData['mother_name'])) {

            //$remote = new RemoteAddress;
            //$allParent = $this->getFirstParent($user_id);
            //$myFatherId = $allParent[0];
            //$myGrandFatherId=$allParent[1];
            //$relationIds = $this->getRelationIds($myFatherId);
            //Debug::dump($familyData);
            //exit;
            $dob = date('Y-m-d', strtotime($familyData['mother_dob']));
            $dod = date('Y-m-d', strtotime($familyData['mother_dod']));
            $profileId = $this->createFamilyProfile($familyData['mother_name'], $familyData['name_title_mother'], $familyData['mother_status'], $dob, $dod, 'Female');
            //exit;
            $this->saveRelation($user_id, $profileId, 'm');
        }

        if ($family_id['spouse_id']) {

            $userData['user_id'] = $family_id['spouse_id'];
            $userData['name_title_user'] = $familyData['name_title_spouse'];
            $userData['full_name'] = $familyData['spouse_name'];
            $userData['live_status'] = $familyData['spouse_status'];
            $userData['dob'] = date('Y-m-d', strtotime($familyData['spouse_dob']));
            $userData['dod'] = date('Y-m-d', strtotime($familyData['spouse_died_on']));
            //$userData['time'] = $familyData;

            $action = new Update('tbl_user_info');
            $action->set($userData);
            $action->where(array('user_id = ?' => $userData['user_id']));
            $stmt = $sql->prepareStatementForSqlObject($action);
            //Debug::dump($userData);
            //exit;
            $result = $stmt->execute();
        } elseif (!empty($familyData['spouse_name'])) {

            //$remote = new RemoteAddress;
            //$allParent = $this->getFirstParent($user_id);
            //$myFatherId = $allParent[0];
            //$myGrandFatherId=$allParent[1];
            //$relationIds = $this->getRelationIds($myFatherId);
            //Debug::dump($familyData);
            //exit;
            $dob = date('Y-m-d', strtotime($familyData['spouse_dob']));
            $dod = date('Y-m-d', strtotime($familyData['spouse_died_on']));
            $profileId = $this->createFamilyProfile($familyData['spouse_name'], $familyData['name_title_spouse'], $familyData['spouse_status'], $dob, $dod, 'Female');
            //exit;
            $this->saveRelation($user_id, $profileId, 'w');
        }

        if ($familyData['numbor'] > '0') {

            for ($i = 0; $i < $familyData['numbor']; $i++) {
                //Debug::dump($familyData['brother_name'][$i]);
                //exit;

                if (!empty($familyData['brother_id'][$i])) {
                    // Debug::dump($familyData);
                    //exit;
                    $bNum = count($familyData['brother_id']);


                    $brotherData['user_id'] = $familyData['brother_id'][$i];
                    $brotherData['name_title_user'] = $familyData['name_title_brother'][$i];
                    $brotherData['full_name'] = $familyData['brother_name'][$i];
                    $brotherData['live_status'] = $familyData['brother_status'][$i];
                    $brotherData['dob'] = date('Y-m-d', strtotime($familyData['brother_dob'][$i]));
                    $brotherData['dod'] = date('Y-m-d', strtotime($familyData['brother_dod'][$i]));
                    //$userData['time'] = $familyData;

                    $actionb = new Update('tbl_user_info');
                    $actionb->set($brotherData);
                    $actionb->where(array('user_id = ?' => $brotherData['user_id']));
                    $stmt = $sql->prepareStatementForSqlObject($actionb);

                    $result = $stmt->execute();
//                     Debug::dump($result);
//                    exit;
                } elseif (!empty($familyData['brother_name'][$i])) {
//                    Debug::dump($familyData);
//                    exit;
                    $allParent = $this->getFirstParent($user_id);
                    $myFatherId = $allParent[0];
                    //Debug::dump($familyData);
                    //exit;
                    $dob = date('Y-m-d', strtotime($familyData['brother_dob'][$i]));
                    $dod = date('Y-m-d', strtotime($familyData['brother_dod'][$i]));
                    $profileId = $this->createFamilyProfile($familyData['brother_name'][$i], $familyData['name_title_brother'][$i], $familyData['brother_status'][$i], $dob, $dod, 'Male');
                    //Debug::dump($myFatherId);
                    //Debug::dump($profileId);
                    //exit;
                    $this->saveRelation($profileId, $myFatherId, 'b');
                }
            }
        } else {

            //$bNum = count($familyData['brother_name']);
            //Debug::dump($bNum);
            //exit;
        }

        if ($familyData['numsis'] > '0') {

            for ($i = 0; $i < $familyData['numsis']; $i++) {
                //Debug::dump($familyData['brother_name'][$i]);
                //exit;

                if (!empty($familyData['sister_id'][$i])) {
                    //Debug::dump($familyData);
                    //exit;
                    $sNum = count($familyData['sister_id']);


                    $sisterData['user_id'] = $familyData['brother_id'][$i];
                    $sisterData['name_title_user'] = $familyData['name_title_sister'][$i];
                    $sisterData['full_name'] = $familyData['sister_name'][$i];
                    $sisterData['live_status'] = $familyData['sister_status'][$i];
                    $sisterData['dob'] = date('Y-m-d', strtotime($familyData['sister_dob'][$i]));
                    $sisterData['dod'] = date('Y-m-d', strtotime($familyData['sister_dod'][$i]));
                    //$userData['time'] = $familyData;

                    $action = new Update('tbl_user_info');
                    $action->set($sisterData);
                    $action->where(array('user_id = ?' => $sisterData['user_id']));
                    $stmt = $sql->prepareStatementForSqlObject($action);
                    //Debug::dump($stmt);
                    //exit;
                    $result = $stmt->execute();
                } elseif (!empty($familyData['sister_name'][$i])) {
//                    Debug::dump($familyData);
//                    exit;
                    $allParent = $this->getFirstParent($user_id);
                    $myFatherId = $allParent[0];
                    //Debug::dump($familyData);
                    //exit;
                    $dob = date('Y-m-d', strtotime($familyData['sister_dob'][$i]));
                    $dod = date('Y-m-d', strtotime($familyData['sister_dod'][$i]));
                    $profileId = $this->createFamilyProfile($familyData['sister_name'][$i], $familyData['name_title_sister'][$i], $familyData['sister_status'][$i], $dob, $dod, 'Female');
                    //Debug::dump($myFatherId);
                    //Debug::dump($profileId);
                    //exit;
                    $this->saveRelation($profileId, $myFatherId, 's');
                }
            }
        } else {

            //$bNum = count($familyData['brother_name']);
            //Debug::dump($bNum);
            //exit;
        }

        if ($familyData['numkid'] > '0') {

            for ($i = 0; $i < $familyData['numkid']; $i++) {
                //Debug::dump($familyData['brother_name'][$i]);
                //exit;

                if (!empty($familyData['kids_id'][$i])) {
                    //Debug::dump($familyData);
                    //exit;
                    $sNum = count($familyData['kids_id']);


                    $kidsData['user_id'] = $familyData['kids_id'][$i];
                    $kidsData['name_title_user'] = $familyData['name_title_kids'][$i];
                    $kidsData['full_name'] = $familyData['kids_name'][$i];
                    $kidsData['live_status'] = $familyData['kids_status'][$i];
                    $kidsData['dob'] = date('Y-m-d', strtotime($familyData['kids_dob'][$i]));
                    $kidsData['dod'] = date('Y-m-d', strtotime($familyData['kids_dod'][$i]));
                    //$userData['time'] = $familyData;

                    $action = new Update('tbl_user_info');
                    $action->set($kidsData);
                    $action->where(array('user_id = ?' => $kidsData['user_id']));
                    $stmt = $sql->prepareStatementForSqlObject($action);
                    //Debug::dump($stmt);
                    //exit;
                    $result = $stmt->execute();
                } elseif (!empty($familyData['kids_name'][$i])) {
//                    Debug::dump($familyData);
//                    exit;
                    $allParent = $this->getFirstParent($user_id);
                    $myFatherId = $allParent[0];
                    //Debug::dump($familyData);
                    //exit;
                    $dob = date('Y-m-d', strtotime($familyData['kids_dob'][$i]));
                    $dod = date('Y-m-d', strtotime($familyData['kids_dod'][$i]));
                    $profileId = $this->createFamilyProfile($familyData['kids_name'][$i], $familyData['name_title_kids'][$i], $familyData['kids_status'][$i], $dob, $dod, 'Male');
                    //Debug::dump($myFatherId);
                    //Debug::dump($profileId);
                    //exit;
                    $this->saveRelation($profileId, $user_id, 'f');
                }
            }
        } else {

            //$bNum = count($familyData['brother_name']);
            //Debug::dump($bNum);
            //exit;
        }




        if ($family_id['grand_father_id']) {

            $userData['user_id'] = $family_id['grand_father_id'];
            $userData['name_title_user'] = $familyData['name_title_grand_father'];
            $userData['full_name'] = $familyData['grand_father_name'];
            $userData['live_status'] = $familyData['grand_father_status'];
            $userData['dob'] = date('Y-m-d', strtotime($familyData['grand_father_dob']));
            $userData['dod'] = date('Y-m-d', strtotime($familyData['grand_father_dod']));
            //$userData['time'] = $familyData;

            $action = new Update('tbl_user_info');
            $action->set($userData);
            $action->where(array('user_id = ?' => $userData['user_id']));
            $stmt = $sql->prepareStatementForSqlObject($action);
            //Debug::dump($userData);
            //exit;
            $result = $stmt->execute();
        } elseif ($familyData['gf'] == 'yes') {

            $remote = new RemoteAddress;
            $allParent = $this->getFirstParent($user_id);

            $myFatherId = $allParent[0];
            //$myGrandFatherId=$allParent[1];
            //$relationIds = $this->getRelationIds($myFatherId);
            //Debug::dump($familyData);
            //exit;
            $dob = date('Y-m-d', strtotime($familyData['grand_father_dob']));
            $dod = date('Y-m-d', strtotime($familyData['grand_father_dod']));
            $profileId = $this->createFamilyProfile($familyData['grand_father_name'], $familyData['name_title_grand_father'], $familyData['grand_father_status'], $dob, $dod, 'Male');
            //exit;
            $this->saveRelation($myFatherId, $profileId, 'f');
        }

        if ($family_id['grand_grand_father_id']) {

            $userData['user_id'] = $family_id['grand_grand_father_id'];
            $userData['name_title_user'] = $familyData['name_title_grand_grand_father'];
            $userData['full_name'] = $familyData['grand_grand_father_name'];
            $userData['live_status'] = $familyData['grand_grand_father_status'];
            $userData['dob'] = date('Y-m-d', strtotime($familyData['grand_grand_father_dob']));
            $userData['dod'] = date('Y-m-d', strtotime($familyData['grand_grand_father_dod']));
            //$userData['time'] = $familyData;

            $action = new Update('tbl_user_info');
            $action->set($userData);
            $action->where(array('user_id = ?' => $userData['user_id']));
            $stmt = $sql->prepareStatementForSqlObject($action);
            //Debug::dump($userData);
            //exit;
            $result = $stmt->execute();
        } elseif ($familyData['ggf'] == 'yes') {

            $remote = new RemoteAddress;
            $allParent = $this->getFirstParent($user_id);

            $myFatherId = $allParent[0];
            $myGrandFatherId = $allParent[1];

            //$relationIds = $this->getRelationIds($myFatherId);
            //Debug::dump($allParent);
            //exit;
            if ($myGrandFatherId == 0) {
                $profileId = $this->createFamilyProfile('', 'Mr.', '', '', '');
                $this->saveRelation($myFatherId, $profileId, 'f');
            }
            $allParent = $this->getFirstParent($user_id);
            $myGrandFatherId = $allParent[1];
            $dob = date('Y-m-d', strtotime($familyData['grand_grand_father_dob']));
            $dod = date('Y-m-d', strtotime($familyData['grand_grand_father_dod']));
            $profileId = $this->createFamilyProfile($familyData['grand_grand_father_name'], $familyData['name_title_grand_grand_father'], $familyData['grand_grand_father_status'], $dob, $dod, 'Male');
            //exit;
            $this->saveRelation($myGrandFatherId, $profileId, 'f');
        }
        if ($family_id['grand_mother_id']) {

            $userData['user_id'] = $family_id['grand_mother_id'];
            $userData['name_title_user'] = $familyData['name_title_grand_mother'];
            $userData['full_name'] = $familyData['grand_mother_name'];
            $userData['live_status'] = $familyData['grand_mother_status'];
            $userData['dob'] = date('Y-m-d', strtotime($familyData['grand_mother_dob']));
            $userData['dod'] = date('Y-m-d', strtotime($familyData['grand_mother_dod']));
            //$userData['time'] = $familyData;

            $action = new Update('tbl_user_info');
            $action->set($userData);
            $action->where(array('user_id = ?' => $userData['user_id']));
            $stmt = $sql->prepareStatementForSqlObject($action);
            //Debug::dump($userData);
            //exit;
            $result = $stmt->execute();
        } elseif ($familyData['gm'] == 'yes') {

            $remote = new RemoteAddress;
            $allParent = $this->getFirstParent($user_id);

            $myFatherId = $allParent[0];
            //$myGrandFatherId=$allParent[1];
            //$relationIds = $this->getRelationIds($myFatherId);
            // Debug::dump($familyData);
            // exit;
            $dob = date('Y-m-d', strtotime($familyData['grand_mother_dob']));
            $dod = date('Y-m-d', strtotime($familyData['grand_mother_dod']));
            $profileId = $this->createFamilyProfile($familyData['grand_mother_name'], $familyData['name_title_grand_mother'], $familyData['grand_mother_status'], $dob, $dod, 'Female');
            //exit;
            $this->saveRelation($myFatherId, $profileId, 'm');
        }
        if ($family_id['grand_grand_mother_id']) {
            //Debug::dump($family_id);
            //exit;
            $userData['user_id'] = $family_id['grand_grand_mother_id'];
            $userData['name_title_user'] = $familyData['name_title_grand_grand_mother'];
            $userData['full_name'] = $familyData['grand_grand_mother_name'];
            $userData['live_status'] = $familyData['grand_grand_mother_status'];
            $userData['dob'] = date('Y-m-d', strtotime($familyData['grand_grand_mother_dob']));
            $userData['dod'] = date('Y-m-d', strtotime($familyData['grand_grand_mother_dod']));


            $action = new Update('tbl_user_info');
            $action->set($userData);
            $action->where(array('user_id = ?' => $userData['user_id']));
            $stmt = $sql->prepareStatementForSqlObject($action);
            //Debug::dump($userData);
            //exit;
            $result = $stmt->execute();
        } elseif ($familyData['ggm'] == 'yes') {

            $remote = new RemoteAddress;
            $allParent = $this->getFirstParent($user_id);

            //$myFatherId = $allParent[0];
            $myGrandFatherId = $allParent[1];

            //$relationIds = $this->getRelationIds($myFatherId);
            // Debug::dump($familyData);
            // exit;
            $dob = date('Y-m-d', strtotime($familyData['grand_grand_mother_dob']));
            $dod = date('Y-m-d', strtotime($familyData['grand_grand_mother_dod']));
            $profileId = $this->createFamilyProfile($familyData['grand_grand_mother_name'], $familyData['name_title_grand_grand_mother'], $familyData['grand_grand_mother_status'], $dob, $dod, 'Female');
            //exit;
            $this->saveRelation($myGrandFatherId, $profileId, 'm');
        }
    }

    public function createFamilyProfile($name = "Unknown", $NameTitle, $liveStatus = 'Alive', $dob, $dod, $gender) {

        $sql = new Sql($this->dbAdapter);
        if (empty($name)) {
            $name = "Unknown";
        }
        //Debug::dump($name);
        //exit;
        $remote = new RemoteAddress;

        $userDataGGM['user_type_id'] = '0';
        $userDataGGM['username'] = time() + rand(11111);
        $userDataGGM['password'] = md5(time() + rand(11111));
        $userDataGGM['email'] = time() + rand(11111);
        $userDataGGM['mobile_no'] = rand(1111111111, 9999999999);
        $userDataGGM['activation_key'] = md5(time());
        $userDataGGM['ip'] = $remote->getIpAddress();
        $userDataGGM['created_date'] = date("Y-m-d H:i:s");
        $userDataGGM['Modified_Date'] = date("Y-m-d H:i:s");
        //Debug::dump($userDataGGM);
        //exit;
        $action = new Insert('tbl_user');
        $action->values($userDataGGM);
        $stmt = $sql->prepareStatementForSqlObject($action);
//        Debug::dump($stmt);
//        exit;
        $result = $stmt->execute();
        //Debug::dump($result);
        //exit;
        if ($result instanceof ResultInterface) {
            if ($newId = $result->getGeneratedValue()) {


                //Debug::dump($newId);
                //exit;
                //$userObject->setUserId($newId);
//                $userDataGGM['name_title_user'] = $familyData['name_title_grand_grand_mother'];
//                $userDataGGM['full_name'] = $familyData['grand_grand_mother_name'];
//                $userDataGGM['live_status'] = $familyData['grand_grand_mother_status'];
//                $userDataGGM['dob'] = date('Y-m-d', strtotime($familyData['grand_grand_mother_dob']));
//                $userDataGGM['dod'] = date('Y-m-d', strtotime($familyData['grand_grand_mother_dod']));
                $ref_no = $this->createReferenceNumber($name, $newId);
                //$userObject->setRefNo($ref_no);
                $action = new Update('tbl_user');
                $action->set(array('ref_no' => $ref_no));
                $action->where(array('id = ?' => $newId));
                $stmt = $sql->prepareStatementForSqlObject($action);
                $result = $stmt->execute();
            }
            if ($newId) {
                $userInfoData['ref_no'] = $ref_no;
                $userInfoData['user_id'] = $newId;
                $userInfoData['user_type_id'] = 0;
                $userInfoData['ip'] = $remote->getIpAddress();
                $userInfoData['full_name'] = $name;
                $userInfoData['name_title_user'] = $NameTitle;
                $userInfoData['live_status'] = $liveStatus;
                $userInfoData['gender'] = $gender;
                if (isset($dob)) {
                    $dob = date('Y-m-d', strtotime($dob));
                } else {
                    $dob = date("0000-00-00");
                }
                if (isset($dod)) {
                    $dod = date('Y-m-d', strtotime($dod));
                } else {
                    $dod = date("0000-00-00");
                }

                $userInfoData['dob'] = $dob;
                $userInfoData['dod'] = $dod;
                $userInfoData['created_date'] = date("Y-m-d H:i:s");
                $userInfoData['modified_date'] = date("Y-m-d H:i:s");

                $action = new Insert('tbl_user_info');
                $action->values($userInfoData);
                $stmt = $sql->prepareStatementForSqlObject($action);
                $result = $stmt->execute();
                if ($result instanceof ResultInterface) {
                    return $userInfoData['user_id'];
                }
            }
        }
    }

    public function saveRelation($user_id, $relation_id, $relation) {
        $sql = new Sql($this->dbAdapter);
        if ($relation == 'f') {

            $action = new Update('tbl_family_relation');
            $action->set(array('father_id' => $relation_id));
            $action->where(array('user_id = ?' => $user_id));
            $stmt = $sql->prepareStatementForSqlObject($action);
            $result = $stmt->execute();
            if ($result instanceof ResultInterface) {
                $parentData['user_id'] = $relation_id;
                $parentData['gender'] = 'Male';
                $action = new Insert('tbl_family_relation');
                $action->values($parentData);
                $stmt = $sql->prepareStatementForSqlObject($action);
                $result = $stmt->execute();
            }
        }
        if ($relation == 'm') {
            $action = new Update('tbl_family_relation');
            $action->set(array('mother_id' => $relation_id));
            $action->where(array('user_id = ?' => $user_id));
            $stmt = $sql->prepareStatementForSqlObject($action);
            $result = $stmt->execute();
            if ($result instanceof ResultInterface) {
                $parentData['user_id'] = $relation_id;
                $parentData['gender'] = 'Female';
                $action = new Insert('tbl_family_relation');
                $action->values($parentData);
                $stmt = $sql->prepareStatementForSqlObject($action);
                $result = $stmt->execute();
            }
        }
        if ($relation == 'w') {
            $action = new Update('tbl_family_relation');
            $action->set(array('wife_id' => $relation_id));
            $action->where(array('user_id = ?' => $user_id));
            $stmt = $sql->prepareStatementForSqlObject($action);
            $result = $stmt->execute();
            if ($result instanceof ResultInterface) {
                $parentData['user_id'] = $relation_id;
                $parentData['gender'] = 'Female';
                $action = new Insert('tbl_family_relation');
                $action->values($parentData);
                $stmt = $sql->prepareStatementForSqlObject($action);
                $result = $stmt->execute();
            }
        }

        if ($relation == 'h') {
            $action = new Update('tbl_family_relation');
            $action->set(array('husband_id' => $relation_id));
            $action->where(array('user_id = ?' => $user_id));
            $stmt = $sql->prepareStatementForSqlObject($action);
            $result = $stmt->execute();
            if ($result instanceof ResultInterface) {
                $parentData['user_id'] = $relation_id;
                $parentData['gender'] = 'Male';
                $action = new Insert('tbl_family_relation');
                $action->values($parentData);
                $stmt = $sql->prepareStatementForSqlObject($action);
                $result = $stmt->execute();
            }
        }

        if ($relation == 'b') {

            $parentData['user_id'] = $user_id;
            $parentData['gender'] = 'Male';
            $action = new Insert('tbl_family_relation');
            $action->values($parentData);
            $stmt = $sql->prepareStatementForSqlObject($action);
            $result = $stmt->execute();

            if ($result instanceof ResultInterface) {
                $action = new Update('tbl_family_relation');
                $action->set(array('father_id' => $relation_id));
                $action->where(array('user_id = ?' => $user_id));
                $stmt = $sql->prepareStatementForSqlObject($action);
                $result = $stmt->execute();
            }
        }
        if ($relation == 's') {

            $parentData['user_id'] = $user_id;
            $parentData['gender'] = 'Female';
            $action = new Insert('tbl_family_relation');
            $action->values($parentData);
            $stmt = $sql->prepareStatementForSqlObject($action);
            $result = $stmt->execute();

            if ($result instanceof ResultInterface) {
                $action = new Update('tbl_family_relation');
                $action->set(array('father_id' => $relation_id));
                $action->where(array('user_id = ?' => $user_id));
                $stmt = $sql->prepareStatementForSqlObject($action);
                $result = $stmt->execute();
            }
        }
    }

    public function getFamilyInfoByRelationId() {

        $statement = $this->dbAdapter->query("SELECT tui.name_title_user, tui.full_name, 
            tui.dob, tui.live_status,
            tui.profile_photo, tui.gender, tui.family_values_status, tui.user_id as user_id,
            tfr.user_id as user_id_rel, tfr.father_id, tfr.mother_id, tfr.wife_id, tfr.husband_id  FROM tbl_user_info as tui LEFT JOIN tbl_family_relation as tfr 
                ON tui.user_id=tfr.user_id WHERE tui.user_id=:user_id");
        $parameters = array(
            'user_id' => $user_id
        );
        $result = $statement->execute($parameters);
        $userInfo = $result->current();
    }

    function getFirstParent($id) {
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_family_relation WHERE user_id=$id");
        $result = $statement->execute();
        $rows = $result->current();
//return $rows;
        $parent = array();
        if (isset($rows['father_id'])) {
            $parent[] = $rows['father_id'];
        }
        //array_merge($parent, $rows['father_id']);
        //return $parent[];
        if ($result->count() > 0) {
            # It has children, let's get them.
            # Add the child to the list of children, and get its subchildren
            $parent = array_merge($parent, $this->getFirstParent($rows['father_id']));
            //echo $this->getFirstParent($rows['father_id']);
        }
        return $parent;
    }

    function getAllChild($id) {
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_family_relation WHERE father_id=$id");
        $result = $statement->execute();
        //$rows = $result->current();
        $resulrSet = $this->resultSet->initialize($result)->toArray();
//return $rows;
        $parent = array();

        $parent[] = $resulrSet;

        //array_merge($parent, $rows['father_id']);
        //return $parent[];
        if ($result->count() > 0) {
            # It has children, let's get them.
            # Add the child to the list of children, and get its subchildren
            $parent = array_merge($parent, $this->getAllChild($resulrSet[0]['user_id']));
            //echo $this->getFirstParent($rows['father_id']);
        }
        return $parent;
    }

    function getMyChild($id) {
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_family_relation WHERE father_id=$id");
        $result = $statement->execute();
        //$rows = $result->current();
        $resulrSet = $this->resultSet->initialize($result)->toArray();
//return $rows;
        $parent = array();

        $parent[] = $resulrSet;

        //array_merge($parent, $rows['father_id']);
        //return $parent[];
        if ($result->count() > 0) {
            # It has children, let's get them.
            # Add the child to the list of children, and get its subchildren
            $parent = array_merge($parent, $this->getAllChild($resulrSet[0]['user_id']));
            //echo $this->getFirstParent($rows['father_id']);
        }
        return $parent;
    }

    function getRelationIds($id) {
        $statement = $this->dbAdapter->query("SELECT * FROM tbl_family_relation WHERE user_id=$id");
        $result = $statement->execute();
        $rows = $result->current();
        return $rows;
    }

}
