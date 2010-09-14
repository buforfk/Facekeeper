<?php

class system_backup extends Event
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
        $start = $this->request->get('page', 1 , bP_INT);
        
        $per = $this->config->get('pager.itemperpage');

         $pager_object = new bPack_Pager();
        $directory = bPack_App_BaseDir . 'backup/';
        $results_count = count(glob("".$directory. "*.bz2"));
        $pager_object->total(ceil($results_count / $per));
        $pager_object->per($per);
        $pager_object->current($start);
        $start = ($start-1) * $per;
        $this->view->assign('pager', $pager_object->output(new bP_Pager_Decorator_Pagi));

        # fetch
        $iterator = new DirectoryIterator(bPack_App_BaseDir . 'backup/');

        $dir = array();
        while($iterator->valid()) {
            if($iterator->isFile())
            {
                $dir[] = $iterator->getFilename();
            }
            $iterator->next();
        }

        sort($dir);

        $dir = array_reverse($dir);

        $directory = array();
        for($y = 0; $y < $per; $y++)
        {
            $location = $start + $y;
            if(isset($dir[$location]))
            {
                $directory[] = $dir[$location];
            }
        }

        $this->view->assign('directory',$directory);

        $this->view->output('system/backup.html');
    }

    public function remove()
    {
        $file = $this->request->get('fn','');
        
        $client = new GearmanClient();
        $client->addServer();
        
        $client->doBackground('delete_backup', json_encode(array('file'=>$file)));


        $this->notifyHelper->set('備份檔 '.$file.' 已刪除。');
        $this->go('system/backup');
    }

    public function now()
    {
        $client = new GearmanClient();
        $client->addServer();
        
        $client->doBackground('system_backup', date('Ymd'));

        $this->notifyHelper->set('備份工作已開始進行，請稍等片刻後再回來看看。');
        $this->go('system/backup');
    }    
    
}
