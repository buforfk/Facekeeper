<?php
class Youtube extends bPack_DB_GenericObject
{
    protected $_table = 'youtube_pool';
    
    public function getAll()
    {
        $results = $this->_get("`id` > 0 ORDER BY `date` DESC, `views` DESC");
                
        return $results;
    }
}
