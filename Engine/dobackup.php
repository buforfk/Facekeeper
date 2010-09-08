<?php
        $client = new GearmanClient();
        $client->addServer();
        
        $client->doBackground('system_backup', date('Ymd'));
 
