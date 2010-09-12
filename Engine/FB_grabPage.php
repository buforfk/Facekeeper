<?php

require_once('/var/www/Facekeeper/lib/bPack/lib/sfYaml/sfYaml.php');

$worker = new GearmanWorker();
$worker->addServer(); // 預設為 localhost
$worker->addFunction('FB_grabPage', 'grabPage');

while($worker->work()) {
    sleep(1); // 無限迴圈，並讓 CPU 休息一下
}

function facebook_login($ch)
{
    $config = sfYaml::load('/var/www/Facekeeper/config/database.yml');
    $ENV = 'development';

    $db = new PDO('mysql:host='.$config[$ENV]['host'].';dbname='.$config[$ENV]['name'], $config[$ENV]['user'] , $config[$ENV]['password']); 
    $db->exec("SET NAMES 'utf8';");

    $data = $db->query("SELECT `value`,`name` FROM `configs` WHERE `name` = 'fb.username' OR `name` = 'fb.password';")->fetchAll(PDO::FETCH_ASSOC);

    $config = array();

    foreach($data as $v) $config[$v['name']] = $v['value'];
    
    $login_email = $config['fb.username'];
    $login_pass = $config['fb.password'];

    curl_setopt($ch, CURLOPT_POST, 1); 
    curl_setopt($ch, CURLOPT_URL, 'https://login.facebook.com/login.php'); 
    curl_setopt($ch, CURLOPT_POSTFIELDS,'email='.urlencode($login_email).'&pass='.urlencode($login_pass).'&login=Login'); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

    curl_exec($ch);
    
    $db->exec("INSERT INTO `logs` SET `daemon` = 'FB_GRABPAGE', `message` = 'FB登入成功', `time` = NOW();");
}

function grabPage($job)
{
    $db = new PDO('mysql:host=localhost;dbname=repu', 'root' , 'bewexos');
    $db->exec("SET NAMES 'utf8';");
    
    $face_cookie = '/var/www/Facekeeper/tmp/FB_Store/cookie.txt';

    $ch = curl_init(); 

    curl_setopt($ch, CURLOPT_COOKIEJAR, $face_cookie); 
    curl_setopt($ch, CURLOPT_COOKIEFILE, $face_cookie); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
                     
    $job_info = json_decode(str_replace("'",'"',str_replace("u'","'",$job->workload())));

    $client = new GearmanClient();
    $client->addServer();

    foreach($job_info->url as $url)
    {
        curl_setopt($ch, CURLOPT_URL,$url);
        echo $url . "\n";
        $content = curl_exec($ch);

        if(strpos($content, 'login') === FALSE)
        {
            file_put_contents('/var/www/Facekeeper/tmp/FB_Store/'. sha1($url).'.html', $content);
            $job_info_be_throw = array('hash'=>sha1($url), 'type' => $job_info->type);
            
            $db->exec("INSERT INTO `logs` SET `daemon` = 'FB_GRABPAGE', `message` = '".$job_info->url."', `time` = NOW();");

            $client->doBackground('FB_parsePage', json_encode($job_info_be_throw));
        }
        else
        {
            facebook_login($ch);
        }
    }

    curl_close($ch);
}
