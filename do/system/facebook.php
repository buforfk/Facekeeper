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
    
    public function enable()
    {
        $this->config->set('fb.enable', 1);
        
        $this->go('system/facebook');
    }

    public function disable()
    {
        $this->config->set('fb.enable', 0);
        
        $this->go('system/facebook');
    }
    
    public function defaultAction()
    {
        $start = $this->request->get('page', 1 , bP_INT);
        
        $per = $this->config->get('pager.itemperpage');

         $pager_object = new bPack_Pager();

        $results_count = $this->db->query("SELECT count(*) as `count` FROM `fb_directories` GROUP BY `url`;")->fetch(PDO::FETCH_ASSOC);
        $pager_object->total(ceil($results_count['count'] / $per));
        $pager_object->per($per);
        $pager_object->current($start);
        $start = ($start-1) * $per;
        $this->view->assign('pager', $pager_object->output(new bP_Pager_Decorator_Pagi));

        # fetch
        $sql = "SELECT * FROM `fb_directories` GROUP BY `url` ORDER BY `tracking` DESC, `type` ASC LIMIT $start,$per;";

        $directories = $this->db->query($sql)->fetchAll();
        $this->view->assign('directory',$directories);

        $this->view->assign('status' , $this->config->get('fb.enable'));
        $this->view->assign('username' , $this->config->get('fb.username'));
        $this->view->assign('password' , $this->config->get('fb.password'));
        $this->view->output('system/facebook.html');
    }

    public function add_track()
    {
        $id = $this->request->get('id', 0, bP_INT);

        $this->db->exec("UPDATE `fb_directories` SET `tracking` = 1 WHERE `id` = '$id';");

        $this->notifyHelper->set('已設定觀察所選之項目(#'.$id.')');
        $this->go('system/facebook');
    }

    public function remove_track()
    {
        $id = $this->request->get('id', 0, bP_INT);

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
            $job_keyword[]= $keyword['keyword'];
        }

        $job_info = array('keyword' => $job_keyword);

        $client->doBackground('FB_fetchFans', json_encode($job_info));
        $client->doBackground('FB_fetchGroup', json_encode($job_info));

        $this->notifyHelper->set('更新工作已開始進行，請稍等片刻後再回來看看。');
        $this->go('system/facebook');
    }    
    
    public function update()
    {
        $this->config->set('fb.username', $this->request->post('username',''));

        $this->config->set('fb.password', $this->request->post('password',''));

        $this->notifyHelper->set('Facebook 帳號設定已更新！');

        $this->go('system/facebook');
    } 
}
