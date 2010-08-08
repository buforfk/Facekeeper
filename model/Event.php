<?php
    class Event extends bPack_Event_Model
    {
        public $view, $db, $auth, $session;
        
        final public function __construct()
        {            
            $this->view = new bPack_View;
            $this->view->setOutputHandler(new bPack_View_Smarty);
            
            $this->session = new bPack_Session;
            
            $this->auth = new Auth($this->session);
            
            $this->request = new bPack_Request;
            
            $this->view->assign('userName', $this->auth->getUsername());
            
            $this->db = bPack_DB::getInstance();
            $this->db->query("SET NAMES 'utf8';");

            $this->config = bPack_Config::getInstance();
            $this->config->setProvider(new bPack_Config_DB($this->db, CONFIG_TABLE));
            
            $this->_registerHelper(new bPack_Event_Helper_Default);
            $this->_registerHelper(new Helper_Notify($this->session));
            $this->_registerHelper(new Helper_FaceKeeper($this));

            $this->view->assign('show_tooltips', $this->config->get('backend.showtooltip'));
            
        }
        
        public function startupAction() {}
        public function defaultAction() {}
        public function tearDownAction() {}
        
        
    }
