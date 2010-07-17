<?php
class Keyword extends bPack_DB_GenericObject
{
    protected $_table = 'keywords';
    
    public function getAll()
    {
        $keywords = $this->_get("`id` > 0");
        
        $keyword = array();
        foreach($keywords as $key)
        {
            $keyword[] = array('id'=>$key['id'],'key'=>$key['keyword']);
        }
        
        return $keyword;
    }
    
    public function add($keyword)
    {
        $this->_setField('keyword',$keyword);
        
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
    
    public function update($item_id, $keyword)
    {
        $this->_setField('keyword',$keyword);
        return $this->_save($item_id);
    }
}