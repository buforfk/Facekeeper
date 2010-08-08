<?php

class system_administrator extends Event
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
        
        $this->FaceKeeperHelper->setPageModule('system');
    }
    
    public function defaultAction()
    {
        $admin_obj = new Administrator($this->db);

        $administrators = $admin_obj->getAll();
        
        $this->view->assign('admin',$administrators);

        $this->view->output('system/administrator/list.html');
    }
    
    public function add()
    {
        $username = $this->request->post('username','');
        $password = $this->request->post('password','');
        
        $admin_obj = new Administrator($this->db);
        
        $admin_obj->add($username, $password);
        
        $this->notifyHelper->set('管理員('.$username.')新增成功');
        $this->adminHelper->log('管理員管理：新增 '.$username.')');
        
        $this->go('system/administrator');
    }
    
    public function delete()
    {
        $item_id = $this->request->get('id','');
        $table_name =  $this->request->get('c','');
        
        $misc_obj = new Administrator($this->db);
        $misc_obj->delete($item_id);
        
        $this->notifyHelper->set('管理員刪除成功');
        $this->adminHelper->log('管理員管理：刪除 '.$item_id);
        
        $this->go('system/administrator');
    }
    
    public function modify()
    {
        $item_id = $this->request->get('id','');

        $admin_obj = new Administrator($this->db);

        
        if($_POST)
        {
            $password = $this->request->post('password','');
            $admin_obj->update($item_id, $password);
            $this->notifyHelper->set('管理員修改成功');
            $this->adminHelper->log('管理員管理：改密碼 '.$item_id);
            $this->go('system/administrator');
        }
        else
        {
            $item_data = $admin_obj->get($item_id);
            
            $this->view->assign($item_data);
            
            $this->view->output('system/administrator/modify.html');    
        }
    }
}
