TRUNCATE TABLE `administrator_logs`;
TRUNCATE TABLE `cron_running_logs`;
TRUNCATE TABLE `logs`;
TRUNCATE TABLE `reports`;
TRUNCATE TABLE `result_pool`;
TRUNCATE TABLE `youtube_pool`;
TRUNCATE TABLE `fb_directories`;
TRUNCATE TABLE `administrators`;

INSERT INTO `administrators` SET `username` = 'admin', `password` = SHA1('admin');
