<?php
class system_default extends Event
{
    public function defaultAction()
    {
        $this->go('system/administrator');
    }
}