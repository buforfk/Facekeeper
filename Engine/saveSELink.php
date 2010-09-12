<?php
// Facekeeper v1.0
// (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>
    require_once('/var/www/Facekeeper/lib/bPack/lib/sfYaml/sfYaml.php');

$worker = new GearmanWorker();
$worker->addServer(); // 預設為 localhost
$worker->addFunction('saveSELink', 'saveLink');

while($worker->work()) {
    sleep(1); // 無限迴圈，並讓 CPU 休息一下
}

function saveLink($job)
{
    $config = sfYaml::load('/var/www/Facekeeper/config/database.yml');
    $ENV = 'development';

    $db = new PDO('mysql:host='.$config[$ENV]['host'].';dbname='.$config[$ENV]['name'], $config[$ENV]['user'] , $config[$ENV]['password']);
    $db->exec("SET NAMES 'utf8';");

    $job_info = json_decode(str_replace("'",'"',$job->workload()));
    
    $data = $db->query("SELECT `title` FROM `result_pool` WHERE `id` = '".$job_info->id."';")->fetch(PDO::FETCH_ASSOC);

    $result = strip_tags($data['title']);

    $db->exec("UPDATE `result_pool` SET `title` = '".$result."' WHERE `id` = '".$job_info->id."';");

    $db->exec("INSERT INTO `logs` SET `daemon` = 'SAVESELINK', `message` = '".$job_info->id."', `time` = NOW();");
    
    return true;
}

