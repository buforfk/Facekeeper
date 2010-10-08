<?php

    define('bPack_App_BaseDir','/var/www/Facekeeper/');
    define('bPack_BASE_URI','/Facekeeper/');

    require("/var/www/Facekeeper/lib/bPack/model/View.php");
    require("/var/www/Facekeeper/lib/bPack/model/View/Smarty.php");
    require_once('/var/www/Facekeeper/lib/bPack/lib/sfYaml/sfYaml.php');

    $config = sfYaml::load('/var/www/Facekeeper/config/database.yml');
    $ENV = 'development';

    $db = new PDO('mysql:host='.$config[$ENV]['host'].';dbname='.$config[$ENV]['name'], $config[$ENV]['user'] , $config[$ENV]['password']);
    $db->exec("SET NAMES 'utf8';");

    $min_pid = $db->query("SELECT MAX(`pid`)-50 AS `min_pid` FROM `result_pool`;")->fetch(PDO::FETCH_OBJ)->min_pid;
    $y_min_pid = $db->query("SELECT MAX(`pid`)-50 AS `min_pid` FROM `youtube_pool`;")->fetch(PDO::FETCH_OBJ)->min_pid;

    $db->exec("DELETE FROM `result_pool` WHERE `pid` < $min_pid;");
    $db->exec("DELETE FROM `youtube_pool` WHERE `pid` < $y_min_pid;");
    $db->exec("DELETE FROM `result_pool` WHERE `title` = '';");
