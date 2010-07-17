<?php
class Router extends bPack_Router
{
    protected function draw()
    {
        $this->map(new bPack_Route_Default);
        
        return true;
    }
}