<?php

require 'Repu.php'; // Base Class
require 'Repu_Analyzer.php';
require 'Repu_SearchEngines.php'; // 搜尋引擎自已的 code

$worker = new GearmanWorker();
$worker->addServer(); // 預設為 localhost
$worker->addFunction('analyzePage', 'doAnalyzerPage');

while($worker->work()) {
    sleep(1); // 無限迴圈，並讓 CPU 休息一下
}


function doAnalyzerPage($job)
{
    $db = new PDO('mysql:host=localhost;dbname=repu', 'root' , 'bewexos');
    $db->exec("SET NAMES 'utf8';");
    
    $path = $job->workload();
    
    /**
     * 分析google頁面
     */
    $analyzer = new Repu_Page_Analyzer;
    // 預設以 Google 模式分析頁面
    $analyzer->setDeafultParser(new Repu_Google);
    // 資料來源 (DB)
    $analyzer->setDataSource($db);
    
    // 從原始碼內 抓出連結
    $page_links = $analyzer->parse(file_get_contents($path));
    // 儲存連結
    $analyzer->saveLinks($page_links);
    // 刪除原本的頁面資訊
    $analyzer->deletePage($path);
    
    echo sha1($path) . ' 分析完成'."\n";
    
    return true;
}


