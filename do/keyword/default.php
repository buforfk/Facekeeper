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
        $type = $this->request->get('t',1);

        $keyword_obj = new Keyword($this->db);
        $keyword_obj->setType($type);
        $items = $keyword_obj->getAll();
        
        $this->view->assign('item',$items);
        $this->view->assign('type',$type);
        
        $this->view->output('keyword/list.html');
    }   
    
    public function search()
    {        
        $type = $this->request->get('t',1);
        $keyword = $this->request->get('keyword','');
        
        if($keyword == '')
        {
            $this->go('keyword?t='.$type);
        }

        $keyword_obj = new Keyword($this->db);
        $keyword_obj->setType($type);
        $items = $keyword_obj->getRows("`keyword` LIKE '%$keyword%'");

        $this->view->assign('item',$items);
        $this->view->assign('type',$type);

        $this->adminHelper->log('查詢關鍵字：「'.$keyword.'」');
        
        $this->view->output('keyword/list.html');
    }
}
