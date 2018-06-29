<?php

date_default_timezone_set('Asia/Shanghai');
define('PHP_FPM_ENV', true);
define('APPLICATION_PATH', dirname(__FILE__));
$path = APPLICATION_PATH . "/conf/application.ini";
$config = parse_ini_file($path, true);
$application = new Yaf\Application($path);
$application->bootstrap()->run();
