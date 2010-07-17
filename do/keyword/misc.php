<?php

class keyword_default extends Admin_Event
{
    public function startupAction()
    {
        if($this->auth->_logonManager->isLogged() == FALSE)
        {
            $this->go('user/login');
        }
        
        if(!$this->notifyHelper->isEmptyBin())
        {
            $notfiyMessage = $this->notifyHelper->get();
            $this->view->assign('NotifyMessage',$notfiyMessage);
        }
    }
        
    public function defaultAction()
    {
        $table_name =  'keyword';
        
        $misc_obj = new Misc($this->db);
        
        $misc_obj->setTable($table_name);
        
        $items = $misc_obj->getAll();
        
        $this->view->assign('item',$items);
        $this->view->assign('c',$table_name);
        $this->view->assign('c_title',$this->_misc_item_titles[$table_name]);
        
        $this->view->output('admin/misc/list.html');
    }
    
    public function add()
    {
        $name = $this->request->post('name','');
        $table_name =  $this->request->get('c','');
        
        $misc_obj = new Misc($this->db);
        
        $misc_obj->setTable($table_name);
        
        $misc_obj->add($name);
        
        $this->notifyHelper->set('項目新增完成');
        $this->go('admin/misc/listItem?c='.$table_name);
    }
    
    public function delete()
    {
        $item_id = $this->request->get('id','');
        $table_name =  $this->request->get('c','');
        
        $misc_obj = new Misc($this->db);
        
        $misc_obj->setTable($table_name);
        $misc_obj->delete($item_id);
        
        $this->notifyHelper->set('項目刪除完成');
        $this->go('admin/misc/listItem?c='.$table_name);
    }
    
    public function modify()
    {
        $item_id = $this->request->get('id','');
        $table_name =  $this->request->get('c','');
        $misc_obj = new Misc($this->db);
        $misc_obj->setTable($table_name);
        
        if($_POST)
        {
            $name = $this->request->post('name','');
            $misc_obj->update($item_id, $name);
            
            $this->notifyHelper->set('項目修改完成');
            $this->go('admin/misc/listItem?c='.$table_name);
        }
        else
        {
            $item_data = $misc_obj->get($item_id);
            
            $this->view->assign('name',$item_data['name']);
            
            $this->view->assign('item_id',$item_id);
            $this->view->assign('c',$table_name);
            $this->view->output('admin/misc/modify.html');    
        }
    }
}