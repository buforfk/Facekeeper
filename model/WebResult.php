<?php
class WebResult extends bPack_DB_GenericObject
{
    protected $_table = 'level';
    
    public function getAll($fetch_count = 50)
    {
        $results = $this->_get("`id` > 0 ORDER BY `re` DESC LIMIT $fetch_count");
                
        return $results;
    }
}