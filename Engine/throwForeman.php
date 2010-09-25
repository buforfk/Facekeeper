<?php
    $client = new GearmanClient();
    $client->addServer();
    $client->doBackground('Foreman', time());
