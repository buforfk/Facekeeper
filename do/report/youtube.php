<?php
class report_youtube extends Event
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
        
        $this->FaceKeeperHelper->setPageModule('report');
    }
    public function defaultAction()
    {
        $youtube_obj = new Youtube($this->db);
        
        $results = $youtube_obj->getAll();
        
        $this->view->assign('result',$results);
        
        $this->view->output('report/youtube.html');
    }

    public function delete()
    {
        $id = $this->request->get('id',0, bP_INT);

        $this->db->exec("DELETE FROM `youtube_pool` WHERE `id` = '".$id."';");

        $this->notifyHelper->set('Youtube影片結果已刪除');

        $this->go('report/youtube');
    }
}
