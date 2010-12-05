<?php
// 定義必需常數
define('bPack_App_BaseDir','/var/www/Facekeeper/');
define('bPack_APP_ENV', (getenv('APP_ENV') ? getenv('APP_ENV') : 'production'));

// 載入相關常數
require_once(bPack_App_BaseDir . 'config/constant.php');
require_once(bPack_App_BaseDir . 'config/'.bPack_APP_ENV.'.config.php');

// 載入 bPack
require_once(bPack_BaseDir . 'model/Loader.php');
bPack_Loader::run();

// 進行 Routing
$route_obj = new Router();

bPack_Dispatcher::run($route_obj->parse());
