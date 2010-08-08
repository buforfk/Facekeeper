<?php
class system_fetch extends Event
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
        $this->view->assign('interval', $this->config->get('fetch.interval'));
        $this->view->assign('depth', $this->config->get('fetch.depth'));
        $this->view->assign('delete_enable', $this->config->get('fetch.delete_enable'));
        //$this->view->assign('keyword_combination', $this->config->get('fetch.keyword_combination'));

        $this->view->assign('report_interval', $this->config->get('report.interval'));

        $this->view->output('system/fetch.html');
    }

    public function update()
    {
        $this->config->set('fetch.interval', $this->request->post('interval',12,bP_INT));
        $this->config->set('fetch.depth', $this->request->post('depth',5,bP_INT));

        $this->config->set('fetch.delete_enable', $this->request->post('delete_enable',0,bP_INT));

        //$this->config->set('fetch.keyword_combination', $this->request->post('keyword_combination',1,bP_INT));
        
        $this->notifyHelper->set('抓取程式設定已更新！');
        $this->adminHelper->log('抓取程式設定已更新！');

        $this->go('system/fetch');
    } 
}
