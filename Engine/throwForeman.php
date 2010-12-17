<?php
    $client = new GearmanClient();
    $client->addServer('localhost');
    $client->doBackground('Foreman', time());
