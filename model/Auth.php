<?php
class Auth extends bPack_Auth
{    
    protected function _setLogonManager()
    {
        $this->_logonManager = new Auth_LogonManager($this);  
    }
    
    public function getUsername()
    {
        $data = $this->getStorageData();
        
        return $data['username'];
    }

    public function getID()
    {
        $data = $this->getStorageData();

        return $data['id'];
    }
}

class Auth_LogonManager extends bPack_Auth_LogonManager
{
    public function login($username, $password)
    {
        $db = bPack_DB::getInstance();
        $result = $db->query("SELECT * FROM `".ADMINISTRATOR_TABLE."` WHERE `username` = '$username' AND `password` = sha1('$password') LIMIT 1;");

        if($result->rowCount() == 1)
        {
            $data = $result->fetch(PDO::FETCH_OBJ);
            $this->_parent_obj->setStorageData(array('username'=>$username, 'id'=>$data->id));
            
            return true;
        }
        else
        {
            return false;
        }
    }
}
