<?php
class Helper_AdminLog extends bPack_Event_Helper
{
        public $name = 'admin';
        
        public function __construct(bPack_Event_Model $parent_obj)
        {
            $this->db = $parent_obj->db;
            $this->auth = $parent_obj->auth;
        }
        
        public function log($message)
        {
            $user = $this->getUserID();

            $sql = "INSERT INTO `administrator_logs` SET `user` = '$user', `message` = '$message' , `time` = NOW();";
            
            $this->db->exec($sql);
        }
        public function getUserID()
        {
            return $this->auth->getUserID();
        }
}
