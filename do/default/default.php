<?php
class default_default extends Event
{
    public function defaultAction()
    {
        $this->go('report/web');
    }
}