<?php
class bPack_View
{
    var $_data = array();
    var $_filename = '';
    var $_options = array();
    var $_outputHandler = null;
    
    public function __construct(bPack_View_Adaptee $handler = null)
    {
        if(!is_null($handler))
        {
            $this->setOutputHandler($handler);
        }
    }
    
    public function getEngine()
    {
        return $this->_outputHandler->getEngine();
    }
    

    public function assign($key, $value = '')
    {
        if(is_array($key))
        {
            foreach($key as $k=>$v)
            {
                $this->_data[$k] = $v;
            }
            
            return $this;
        }
        
        $this->_data[$key] = $value;

        return $this;
    }

    public function render($filename = '')
    {
        if(!is_object($this->_outputHandler))
        {
            throw new bPack_Exception('bPack View: No output handler was specified.');
        }

        foreach($this->_options as $key=>$value)
        {
            $this->_outputHandler->setOption($key,$value);
        }

        foreach($this->_data as $key=>$value)
        {
            $this->_outputHandler->assign($key,$value);
        }

        if($filename != '')
        {
            $this->setFilename($filename);
        }

        if(!empty($this->_filename))
        {
            $this->_outputHandler->setFilename($this->_filename);
        }
        
        $content = $this->_outputHandler->output();
        
        return $content;
    }

    public function output($filename = '')
    {
        echo $this->render($filename);
        return true;
    }

    public function setOption($key, $value = '')
    {
        $this->_options[$key] = $value;
        return $this;
    }

    public function setFilename($filename)
    {
        $this->_filename = $filename;
        return $this;
    }

    public function setOutputHandler(bPack_View_Adaptee $handler)
    {
        $handler->setParent($this);
        $this->_outputHandler = $handler;
        return $this;
    }
    
    public function clearAssignment()
    {
        $this->_data = array();
        return $this;
    }
}

interface bPack_View_Adaptee
{
    public function assign($key,$value = '');
    public function output();
    public function setOption($key, $value = '');
    public function setFilename($filename);
    public function setParent(bPack_View $parent);
    public function getEngine();
}