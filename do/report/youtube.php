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
        $start = $this->request->get('page', 1 , bP_INT);
        $per = $this->config->get('pager.itemperpage');

        $pager_object = new bPack_Pager();

        $results_count = $this->db->query("SELECT count(*) as `count` FROM `youtube_pool` WHERE `pid` = (SELECT MAX(`pid`) FROM `youtube_pool`);")->fetch(PDO::FETCH_ASSOC);
        $pager_object->total(ceil($results_count['count'] / $per));
        $pager_object->per($per);
        $pager_object->current($start);
        
        $start = ($start-1) * $per;
        $this->view->assign('pager', $pager_object->output(new bP_Pager_Decorator_Pagi));
        # 收錄資料統計
        $results = $this->db->query("SELECT * FROM `youtube_pool` WHERE `pid` = (SELECT MAX(`pid`) FROM `youtube_pool`) ORDER BY `date` DESC,`views` DESC LIMIT $start,$per")->fetchAll(PDO::FETCH_ASSOC);

        $this->view->assign('result' , $results);

        
        $this->view->output('report/youtube.html');
    }

    public function delete()
    {
        $id = $this->request->get('id',0, bP_INT);

        $this->db->exec("DELETE FROM `youtube_pool` WHERE `id` = '".$id."';");

        $this->notifyHelper->set('Youtube影片(#'.$id.')結果已刪除');
        $this->adminHelper->log('Youtube影片結果：已刪除 '.$id);

        $this->go('report/youtube');
    }

    public function bulk_delete()
    {
        $response = array();
        foreach($_POST as $k=>$v)
        {
            if(substr($k, 0, 4) == 'item')
            {
                $this->db->exec("DELETE FROM `youtube_pool` WHERE `id` = '".$v."';");
                $response['success'][] = $v;
            }
        }

        $this->notifyHelper->set(sizeof($response['success']) . ' 個項目已刪除');
        $this->adminHelper->log('Youtube影片結果：已刪除 '.sizeof($response['success'])  . '個');

        echo json_encode($response);
        exit;
    }
}
