<?php
define('bP_NOTIFY_INFO',0);
define('bP_NOTIFY_ERROR',1);

class Helper_Notify extends bPack_Event_Helper
{
    public $name = 'notify';
    
    protected $_storage_bin = null;
    protected $_storage_name = null;
    
    /**
     * 起始設定儲存地點
     */
    public function __construct($session_obj = null, $session_name = 'notifyStorage')
    {
        if($session_obj == null)
        {
            throw new bPack_Exceptioin('Helper_Notify: No stoage bin were given.');
        }
        $this->_storage_bin = $session_obj;
        $this->_storage_name = $session_name;
        
    }
    
    /**
     * 訊息匣是否是空的？
     */
    public function isEmptyBin()
    {
        if(is_array($this->_storage_bin->get($this->_storage_name)))
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    
    /**
     * 取得訊息
     */
    public function get()
    {
        if(!$this->isEmptyBin())
        {
            $notfiyMessageInformation = $this->_storage_bin->get($this->_storage_name);
            $this->_clearBin();
            
            return $notfiyMessageInformation;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * 設定訊息
     */
    public function set($message, $message_type = bP_NOTIFY_INFO)
    {
        $this->_storage_bin->clear($this->_storage_name);
        
        $this->_storage_bin->set($this->_storage_name, array('message'=>$message, 'type'=>$message_type));
        
        return true;
    }
    
    /**
     * 清除訊息匣
     */
    protected function _clearBin()
    {
        $this->_storage_bin->clear($this->_storage_name);
    }
}