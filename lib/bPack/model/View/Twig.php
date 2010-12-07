<?php
require_once bPack_App_BaseDir . 'lib/Twig/Autoloader.php';
Twig_Autoloader::register();
 
class bPack_View_Twig implements bPack_View_Adaptee
{
    protected $_parent;
    protected $twig;
    protected $_filename;
    protected $values = array();
 

    public function setOption($a,$b='')

    {
    }

    public function __construct()
    {
        $loader = new Twig_Loader_Filesystem(bPack_App_BaseDir . 'tpl');
        $this->twig = new Twig_Environment($loader, array(
          'cache' => bPack_App_BaseDir . 'tmp/',
        ));

        $this->twig->addExtension(new bP_Twig_Truncate());

        $this->assign('bPack_rootpath',bPack_BASE_URI);
    }
    
    public function assign($key,$value = '')
    {
        $this->values[$key] = $value;

        return true;
    }
    
    public function output()
    {
        if(empty($this->_filename))
        {
            throw new bPack_Exception('bPack_View_Twig: No template file are specified to display.');
        }

        $tpl= $this->twig->loadTemplate($this->_filename);
    
        return $tpl->render($this->values);
    }
    
    public function setFilename($filename)
    {
        $this->_filename = $filename;
    
        return true;
    }
    
    public function setParent(bPack_View $parent)
    {
        $this->_parent = $parent;
    
        return true;
    }
    
    public function getEngine()
    {
        return $this->twig;
    }
}

class bP_Twig_Truncate extends Twig_Extension
{
    public function getName()
    {
        return 'truncate';
    }

    public function getFilters()
    {
        return array(
            'truncate' => new Twig_Filter_Function('bP_Twig_Truncate::truncate')
        );
    }

    public static function truncate($string, $length= 40, $encoding = "utf-8")
    {
        if (mb_strlen($string,$encoding) > $length) 
        {
            $length -= mb_strlen($string);
            return $string = mb_substr($string,0,$length,$encoding);
        }
        else
        {
            return $string;
        }
    }
}
