<?php
// Facekeeper v1.0
// (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>

$worker = new GearmanWorker();
$worker->addServer(); // 預設為 localhost
$worker->addFunction('encodePage', 'encodePage');

while($worker->work()) {
    sleep(1); // 無限迴圈，並讓 CPU 休息一下
}

function encodePage($job)
{
    $db = new PDO('mysql:host=localhost;dbname=repu', 'root' , 'bewexos');
    $db->exec("SET NAMES 'utf8';");

    $job_info = json_decode(str_replace("'",'"',str_replace("u'","'",$job->workload())));
    $file =  file_get_contents("../tmp/Page_store/" . $job_info->pid . "/" . $job_info->url .".html");

    preg_match('/charset=(.*?)"/', $file, $matches);

    if(sizeof($matches) > 0)
    {
        $charset = str_replace('-','',strtolower($matches[1]));
        
        if($charset != 'utf8')
        {
            if($charset == 'chinesebig5_charset') 
            {
                $charset = 'big5';
                $charset_replacement = 'chinesebig5_charset';
            }
            else
            {
                $charset_replacement = $charset;
            }

            @file_put_contents("../tmp/Page_store/" . $job_info->pid . "/" . $job_info->url .".html", str_replace($charset_replacement,'utf-8',iconv($charset, 'utf-8', $file)));
        }

        $db->exec("INSERT INTO `logs` SET `daemon` = 'ENCODEPAGE', `message` = '".$job_info->url."(".$charset.")', `time` = NOW();");
    }
    else
    {
        $db->exec("INSERT INTO `logs` SET `daemon` = 'ENCODEPAGE', `message` = '".$job_info->url."(SKIPPED)', `time` = NOW();");
    }

    $client = new GearmanClient();
    $client->addServer();
    $client->doBackground('matchKeyword', json_encode($job_info));
    
    return true;
}

