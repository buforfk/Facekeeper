<?php
    $client = new GearmanClient();
    $client->addServer();
    $job_info = array('hash'=>'d39538664ce4bfd2a0e168c5af84a03c2be527b0','type'=>1);
    $client->doBackground('FB_parsePage', json_encode($job_info));
