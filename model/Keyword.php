<?php
class Keyword extends bPack_DB_GenericObject
{
    protected $_table = 'keywords';
    protected $_type = 1;

    public function setType($type)
    {
        $this->_type = $type;
    }

    public function getAll()
    {
        $keywords = $this->_get("`id` > 0 AND `type` = '".$this->_type."'");

        return $this->processKeyword($keywords);
    }

    public function processKeyword($keywords)
    {

        $administrators = $this->query("SELECT * FROM `administrators`;");

        $admins = array();
        foreach($administrators as $admin)
        {
            $admins[$admin['id']] = $admin['username'];
        }
        
        $keyword = array();
        foreach($keywords as $key)
        {
            $keyword[] = array('id'=>$key['id'],'key'=>$key['keyword'],'creator'=>$admins[$key['creator']],'created_at'=>$key['created_at']);
        }
        
        return $keyword;
    }
    
    public function add($keyword, $administrator)
    {
        $this->_setField('keyword',$keyword);
        $this->_setField('type',$this->_type);
        $this->_setField('creator',$administrator);
        $this->_setField('created_at',date('Y-m-d H:i:s'));
        
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

    public function getRows($item_id)
    {
        $keywords = $this->_get($item_id);

        return $this->processKeyword($keywords);
    }
    
    public function update($item_id, $keyword)
    {
        $this->_setField('keyword',$keyword);
        return $this->_save($item_id);
    }
}
