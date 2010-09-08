<?php
class system_source extends Event
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
        $source_lists = array('Google','Yahoo','Bing','Youtube');
        $source_list = array();
        foreach($source_lists as $source)
        {
            $source_list[] = array('name' => $source, 'status'=> $this->config->get('source.'.strtolower($source).'.enable'));
        }
        $this->view->assign('source', $source_list);
        $this->view->output('system/source.html');
    }

    public function deactivate()
    {
        $source_to_be_deacivated = $this->request->get('source','');

        $this->config->set('source.'.$source_to_be_deacivated.'.enable', 0);

        $this->notifyHelper->set('來源 ['.$source_to_be_deacivated.'] 已停用');
        $this->adminHelper->log('來源管理；停用來源 ['.$source_to_be_deacivated.']');

        $this->go('system/source');
    } 
    
    public function activate()
    {
        $source_to_be_acivated = $this->request->get('source','');

        $this->config->set('source.'.$source_to_be_acivated.'.enable', 1);

        $this->notifyHelper->set('來源 ['.$source_to_be_acivated.'] 已啟用');
        $this->adminHelper->log('來源管理；啟用來源 ['.$source_to_be_acivated.']');

        $this->go('system/source');
    }
}
