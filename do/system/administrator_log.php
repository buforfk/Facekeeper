<?php

class system_administrator_log extends Event
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
        $this->view->assign('log',$this->db->query("SELECT `al`.*,`a`.`username` as `user_name` FROM `administrator_logs` `al` LEFT JOIN `administrators` `a` ON `a`.`id` = `al`.`user` ORDER BY `id` DESC LIMIT 150;")->fetchAll(PDO::FETCH_ASSOC));

        $this->view->output('system/administrator/log.html');
    }
}
