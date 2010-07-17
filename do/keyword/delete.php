<?php
class keyword_delete extends Event
{
    public function defaultAction()
    {
        $keyword_id = $this->request->get('id',0,bP_INT);
        
        $keyword_obj = new Keyword($this->db);
        $data = $keyword_obj->get($keyword_id);
        $keyword_obj->delete($keyword_id);
        
        $this->notifyHelper->set('關鍵字「'.urldecode($data['keyword']).'」已刪除');
        $this->go('keyword');
    }
}