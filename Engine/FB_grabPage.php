<?php
$worker = new GearmanWorker();
$worker->addServer(); // 預設為 localhost
$worker->addFunction('FB_grabPage', 'grabPage');

while($worker->work()) {
    sleep(1); // 無限迴圈，並讓 CPU 休息一下
}

function facebook_login($ch)
{
      $login_email='love29713871@yahoo.com.tw';
    $login_pass='qoo74520';

    curl_setopt($ch, CURLOPT_POST, 1); 
    curl_setopt($ch, CURLOPT_URL, 'https://login.facebook.com/login.php'); 
    curl_setopt($ch, CURLOPT_POSTFIELDS,'email='.urlencode($login_email).'&pass='.urlencode($login_pass).'&login=Login'); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

    curl_exec($ch);
}

function grabPage($job)
{
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
            $client->doBackground('FB_parsePage', json_encode($job_info_be_throw));
        }
        else
        {
            facebook_login($ch);
        }
    }

    curl_close($ch);
}
