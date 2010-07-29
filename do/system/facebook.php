<?php

class system_facebook extends Event
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
        $sql = "SELECT * FROM `fb_directories` ORDER BY `tracking` DESC LIMIT 0,50;";

        $directories = $this->db->query($sql)->fetchAll();

        $this->view->assign('directory',$directories);

        $this->view->output('system/facebook.html');
    }

    public function add_track()
    {
        $id = $this->request('id', 0, bP_INT);

        $this->db->exec("UPDATE `fb_directories` SET `tracking` = 1 WHERE `id` = '$id';");

        $this->notifyHelper->set('已設定觀察所選之項目(#'.$id.')');
        $this->go('system/facebook');
    }

    public function remove_track()
    {
        $id = $this->request('id', 0, bP_INT);

        $this->db->exec("UPDATE `fb_directories` SET `tracking` = 0 WHERE `id` = '$id';");

        $this->notifyHelper->set('已停止觀察所選之項目(#'.$id.')');
        $this->go('system/facebook');
    }

    public function renew_list()
    {
        $sql = "SELECT `keyword` FROM `keywords` WHERE `type` = 3;";
        $keywords = $this->db->query($sql)->fetchAll();
        
        $client = new GearmanClient();
        $client->addServer();
        
        foreach($keywords as $keyword)
        {
            $job_info = array('keyword' => $keyword);

            $client->doBackground('FB_fetchFans', json_encode($job_info));
            $client->doBackground('FB_fetchGroup', json_encode($job_info));
        }

        $this->notifyHelper->set('更新工作已開始進行，請稍等片刻後再回來看看。');
        $this->go('system/facebook');
    }
}
