<?php
class system_report extends Event
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
        $this->view->assign('interval', $this->config->get('report.interval'));
        $this->view->assign('receiver', $this->config->get('report.receiver'));
        $this->view->assign('format', $this->config->get('report.format'));

        $this->view->assign('report_status' , $this->config->get('report.enable'));
        $this->view->output('system/report.html');
    }

    public function enable()
    {
        $this->config->set('report.enable', 1);
        
        $this->go('system/report');
    }

    public function disable()
    {
        $this->config->set('report.enable', 0);
        
        $this->go('system/report');
    }

    public function update()
    {
        $this->config->set('report.interval', $this->request->post('interval',7,bP_INT));

        $this->config->set('report.format', $this->request->post('format','TXT'));

        $this->config->set('report.receiver', $this->request->post('receiver',''));
        
        $this->notifyHelper->set('報表設定已更新！');
        $this->adminHelper->log('報表設定已更新！');

        $this->go('system/report');
    } 
}
