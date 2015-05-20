<?php
require_once 'vendor/autoload.php';

if(!defined('DS')){
	define('DS', DIRECTORY_SEPARATOR);
}
define('ROOT', dirname(__DIR__) . DS);
define('APP', ROOT . 'tests' . DS . 'test_files' . DS);
define('TMP', sys_get_temp_dir() . DS);
define('WEBROOT', APP);
define('WWW_ROOT', APP);

date_default_timezone_set('UTC');
mb_internal_encoding('UTF-8');