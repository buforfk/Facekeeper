<?php

class system_ptt extends Event
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
        $this->config->set('ptt.enable', 1);
        
        $this->go('system/ptt');
    }

    public function disable()
    {
        $this->config->set('ptt.enable', 0);
        
        $this->go('system/ptt');
    }

    public function add()
    {
        $board = $this->request->post('board','');

        $this->db->query("INSERT INTO `ptt_boards` SET `board` = '$board';");

        $this->notifyHelper->set("$board 新增完成");
        $this->adminHelper->log("PTT 管理：$board 新增完成");

        $this->go('system/ptt');
    }
    
    public function remove()
    {
        $id = $this->request->get('id',0 ,bP_INT);

        $this->db->query("DELETE FROM `ptt_boards` WHERE `id` = '$id';");

        $this->notifyHelper->set("刪除完成");

        $this->go('system/ptt');
    }
    public function defaultAction()
    {
        $start = $this->request->get('page', 1 , bP_INT);
        
        $per = $this->config->get('pager.itemperpage');

         $pager_object = new bPack_Pager();

        $results_count = $this->db->query("SELECT count(*) as `count` FROM `ptt_boards`;")->fetch(PDO::FETCH_ASSOC);
        $pager_object->total(ceil($results_count['count'] / $per));
        $pager_object->per($per);
        $pager_object->current($start);
        $start = ($start-1) * $per;
        $this->view->assign('pager', $pager_object->output(new bP_Pager_Decorator_Pagi));

        # fetch
        $sql = "SELECT * FROM `ptt_boards` ORDER BY `board` ASC LIMIT $start,$per;";

        $boards = $this->db->query($sql)->fetchAll();
        $this->view->assign('board',$boards);

        $this->view->assign('status' , $this->config->get('ptt.enable'));
        $this->view->output('system/ptt.html');
    }
}
