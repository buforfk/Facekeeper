<?php
class WebKeyResult extends bPack_DB_GenericObject
{
    protected $_table = 'content_2';
    
    public function getAll()
    {
        $results = $this->_get("`id` > 0 ORDER BY `num` DESC, `re` DESC");
                
        return $results;
    }
}