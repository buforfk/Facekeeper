<?php
class Administrator extends bPack_DB_GenericObject
{
    protected $_table = 'administrators';
    
    public function getAll()
    {
        return $this->_get("`id` > 0");
    }
    
    public function add($username, $password)
    {
        $this->_setField('username',$username);
        $this->_setField('password', sha1($password));
        
        return $this->_save();
    }
    
    public function delete($item_id)
    {
        return $this->_delete($item_id);
    }
    
    public function get($item_id)
    {
        return $this->_getRow($item_id);
    }
    
    public function update($item_id, $password)
    {
        $this->_setField('password',sha1($password));
        return $this->_save($item_id);
    }
}