<?php
use Cake\Core\Configure;

require_once 'vendor/autoload.php';

if(!defined('DS')){
	define('DS', DIRECTORY_SEPARATOR);
}
define('ROOT', dirname(__DIR__) . DS . 'tests' . DS . 'TestApp' . DS);
define('APP', ROOT . 'src' . DS);
define('TMP', sys_get_temp_dir() . DS);
define('WEBROOT', ROOT . 'webroot' . DS);
define('WWW_ROOT', ROOT . 'webroot' . DS);

date_default_timezone_set('UTC');
mb_internal_encoding('UTF-8');

//Configure::write('debug', true);
Configure::write('App', [
    'namespace' => 'TestApp',
    'dir' => 'src'
]);
