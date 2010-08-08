<?php
class user_login extends Event
{
    /**
     * 起始動作
     *  - 檢查是否登入
     *  - 檢查是否有需要提示的訊息
     */
    public function startupAction()
    {        
        if(!$this->notifyHelper->isEmptyBin())
        {
            $notifyMessage = $this->notifyHelper->get();
            
            $this->view->assign('notifyMessage', $notifyMessage['message']);
        }
    }
    
    /**
     * 檢查是否登入，不寫在起始動作是因為本頁也作為登出用途
     */
    protected function _checkIfLogged()
    {
        if($this->auth->_logonManager->isLogged() !== false)
        {
            $this->notifyHelper->set('為什麼你要再重新登入呢？'); 
            $this->go('report/web');
        }
    }
    
    /**
     * 預設動作：顯示登入框
     *  - 
     */
    public function defaultAction()
    {
        $this->_checkIfLogged();
        
        $this->view->output('login.html');
    }
    
    public function login()
    {
        $this->_checkIfLogged();
        
        $username = $this->request->post('username','');
        $password = $this->request->post('password','');
        
        $login_result = $this->auth->_logonManager->login($username,$password);
        
        if($login_result)
        {
            $this->go('report/web');
        }
        else
        {
            $this->notifyHelper->set('登入失敗', bP_NOTIFY_ERROR);
            $this->go('report/web');
        }
    }

    public function ajax_login()
    {
        if($this->auth->_logonManager->islogged() !== false)
        {
            $response = array('message_code' => -1);
        }
        else
        {
            $username = $this->request->post('username','');
            $password = $this->request->post('password','');
            
            $login_result = $this->auth->_logonManager->login($username,$password);
            
            if($login_result)
            {
                $response = array('message_code' => 1);
            }
            else
            {
                $response = array('message_code' => 0);
            }
        }
        
        echo json_encode($response);
        exit;
    }
}
