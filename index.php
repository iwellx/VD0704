<?php
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
define('APP_DEBUG',true);
define('APP_PATH','./App/');
define('WEB_SITE','http://120.27.200.37/vd_service/');
define('RUNTIME_PATH','./Runtime/');
require './ThinkPHP/ThinkPHP.php';