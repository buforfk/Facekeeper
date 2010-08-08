<?php
class system_backend extends Event
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
        $this->view->assign('tooltip', $this->config->get('backend.showtooltip'));
        $this->view->assign('pager', $this->config->get('pager.itemperpage'));
        $this->view->output('system/backend.html');
    }

    public function update()
    {
        $this->config->set('backend.showtooltip', $this->request->post('tooltip',0,bP_INT));
        $this->config->set('pager.itemperpage', $this->request->post('pager',1,bP_INT));
        
        $this->notifyHelper->set('Facekeeper 後台設定已更新！');

        $this->go('system/backend');
    } 
}
