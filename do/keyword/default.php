<?php
class keyword_default extends Event
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
        
        $this->FaceKeeperHelper->setPageModule('keyword');
    }
    
    public function defaultAction()
    {        
        $keyword_obj = new Keyword($this->db);
        
        $items = $keyword_obj->getAll();
        
        $this->view->assign('item',$items);
        
        $this->view->output('keyword/list.html');
    }
}