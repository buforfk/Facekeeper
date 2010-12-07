<?php
/**
 * bPack_View_JSON: JSON-output handler for bPack_View
 *
 * @package bPack
 * @subpackage bPack_View
 * @author bu <bu@hax4.in>
 */

class bPack_View_JSON implements bPack_View_Adaptee
{
    protected $_data_set = null;
    
    public function __construct()
    {
        $this->_data_set = new bPack_DataContainer();
    }
    
    public function assign($key,$value = '')
    {
        $this->_data_set->{$key} = $value;
        return true;
    }

    public function output()
    {
        return json_encode($this->_data_set);
    }

    public function setFilename($filename)
    {
        return true;
    }

    public function setOption($key,$value = '')
    {
        return true;
    }

    public function setParent(bPack_View $parent)
    {
        $this->_parent = $parent;

        return true;
    }
    
    public function getEngine()
    {
        return true;
    }
}