<?php
class Helper_FaceKeeper extends bPack_Event_Helper
{
        public $name = 'FaceKeeper';
        
        public function __construct(bPack_Event_Model $parent_obj)
        {
            $this->view = $parent_obj->view;
            $this->engine = $parent_obj->view->getEngine();
            
            $this->db = $parent_obj->db;
            $this->request = $parent_obj->request;
        }
        
        public function registerModifier()
        {
            $this->engine->register->modifier('airpad_order_flag', array($this, 'order_flag'));
            $this->engine->register->modifier('airpad_payment_type', array($this, 'payment_type'));
        }
        public function setPageModule($module_name)
        {
            $this->view->assign('pageModule',$module_name);
        }
}