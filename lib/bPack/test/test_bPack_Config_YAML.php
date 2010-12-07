<?php
define('bPack_BaseDir' , '../../');
require bPack_BaseDir . 'model/Config/Provider.php';
require bPack_BaseDir . 'model/Config/YAML.php';

class test_bPack_Config_YAML extends bTest_Test
{
    protected $_config_obj = null;
    
    public function test_startup()
    {
        $this->_config_obj = new bPack_Config_YAML('test_bPack_Config_YAML.yml');
    }
    
    public function case_getConfigWithoutNamespace()
    {
        $this->assert_equal(date('Y-m-d',$this->_config_obj->get('last_updated')), '2010-03-21');
    }
    
    public function case_getConfigWithNamespace()
    {
        $this->assert_equal($this->_config_obj->get('development.host'), 'localhost');
    }
}