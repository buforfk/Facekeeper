<?php
class report_log extends Event
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
        $logs = $this->db->query("SELECT * FROM `logs` ORDER BY `id` DESC LIMIT 150")->fetchAll(PDO::FETCH_ASSOC);

        $this->view->assign('log' , $logs);

        $this->view->output('report/log.html');
    }

    public function cleanup()
    {
        $this->db->exec("TRUNCATE TABLE `logs`;");

        $this->notifyHelper->set('記錄已全部清空');

        $this->go('report/log');
    }
}
