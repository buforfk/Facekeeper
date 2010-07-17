<?php

class keyword_add extends Event
{

    public function startupAction()
    {
	if ($this->auth->_logonManager->isLogged() == FALSE)
	{
	    $this->go('user/login');
	}

	if (!$this->notifyHelper->isEmptyBin())
	{
	    $notfiyMessage = $this->notifyHelper->get();
	    $this->view->assign('NotifyMessage', $notfiyMessage);
	}

	$this->FaceKeeperHelper->setPageModule('keyword');
    }

    public function defaultAction()
    {
	$this->view->output('keyword/add.html');
    }

    public function add()
    {
	$keywords = $this->request->post('keyword', array(), bP_STRING, bP_ARRAY);

        $this->add_action($keywords);
    }

    protected function add_action($keywords)
    {
	$keyword_obj = new Keyword($this->db);
	$add_count = 0;

	foreach ($keywords as $keyword)
	{
	    if ($keyword !== '')
	    {
		$keyword_obj->add($keyword);
		$add_count++;
	    }
	}

	$this->notifyHelper->set('已新增 ' . $add_count . ' 個關鍵字');
	$this->go('keyword/add');
    }

    public function add_upload()
    {
        $content = file($_FILES['txt']['tmp_name']);
        $keyword = array();
        
        foreach($content as $line)
        {
            $keyword[] = trim($line);
        }
        
        $this->add_action($keyword);
    }

}
