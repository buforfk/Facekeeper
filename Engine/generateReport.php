<?php

    define('bPack_App_BaseDir','/var/www/Facekeeper/');
    define('bPack_BASE_URI','/Facekeeper/');

    require("/var/www/Facekeeper/lib/bPack/model/View.php");
    require("/var/www/Facekeeper/lib/bPack/model/View/Smarty.php");
    require_once('/var/www/Facekeeper/lib/bPack/lib/sfYaml/sfYaml.php');

    $config = sfYaml::load('/var/www/Facekeeper/config/database.yml');
    $ENV = 'production';

    $db = new PDO('mysql:host='.$config[$ENV]['host'].';dbname='.$config[$ENV]['name'], $config[$ENV]['user'] , $config[$ENV]['password']);
    $db->exec("SET NAMES 'utf8';");

    $view = new bPack_View;
    $view->setOutputHandler(new bPack_View_Smarty);

    # keyword 對應
    $results = $db->query("SELECT * FROM `result_keyword` ORDER BY `pid` DESC,`keyword_length` DESC LIMIT 0, 50")->fetchAll(PDO::FETCH_ASSOC);

    $view->assign('keyword_result' , $results);


    # 次數統計
    $results = $db->query("SELECT * FROM `result_counting` ORDER BY `pid` DESC,`count` DESC LIMIT 0,50")->fetchAll(PDO::FETCH_ASSOC);

    $view->assign('counting_result' , $results);

    # Facebook
    $results = $db->query("SELECT * FROM `result_pool` WHERE `source` = 1 ORDER BY `pid` DESC, `keyword_length` DESC LIMIT 0,50")->fetchAll(PDO::FETCH_ASSOC);
    $view->assign('facebook_results' , $results); 
        
    # 輸出
    file_put_contents(bPack_App_BaseDir . 'tmp/Report/'.date('Ymd').'.html', $view->render('report/generate.html'));

    $db->exec("INSERT INTO `reports` SET `filename` = '".date('Ymd').".html', `time` = NOW(), `filesize` = '".filesize(bPack_App_BaseDir . 'tmp/Report/'.date('Ymd') .'.html')."';");

    file_put_contents(bPack_App_BaseDir . 'tmp/Report/'.date('Ymd').'.xml', $view->render('report/generate.xml'));

    $db->exec("INSERT INTO `reports` SET `filename` = '".date('Ymd').".xml', `time` = NOW(), `filesize` = '".filesize(bPack_App_BaseDir . 'tmp/Report/'.date('Ymd') .'.xml')."';");

    // 寄信

