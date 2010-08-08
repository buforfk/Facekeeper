<?php
class user_logout extends Event
{
    protected function _checkIfLogged()
    {
        if($this->auth->_logonManager->isLogged() === FALSE)
        {
            $this->notifyHelper->set('你還沒登入就登出啦？'); 

            $this->go('user/login');
        }
    }

    public function defaultAction()
    {
        $this->_checkIfLogged();
        
        $this->adminHelper->log('登出');
 
        $this->auth->_logonManager->logout();

        
        $this->go('user/login');
    }
}
