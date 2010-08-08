<?php
class keyword_delete extends Event
{
    public function defaultAction()
    {
        $keyword_id = $this->request->get('id',0,bP_INT);
        
        $keyword_obj = new Keyword($this->db);
        $data = $keyword_obj->get($keyword_id);
        $keyword_obj->delete($keyword_id);
        
        $this->adminHelper->log('刪除關鍵字「'.urldecode($data['keyword']).'」');
        $this->notifyHelper->set('關鍵字「'.urldecode($data['keyword']).'」已刪除');
        $this->go('keyword?t=' . $data['type']);
    }

    public function bulk_delete()
    {
        $keyword_obj = new Keyword($this->db);
        
        $response = array();
        foreach($_POST as $k=>$v)
        {
            if(substr($k, 0, 4) == 'item')
            {
                $keyword_obj->delete($v);
                $response['success'][] = $v;
            }
        }

        $this->notifyHelper->set(sizeof($response['success']) . ' 個關鍵字已刪除');
        $this->adminHelper->log('刪除關鍵字「'.sizeof($response['success']) . ' 個');
        
        echo json_encode($response);
        exit;
    }
}
