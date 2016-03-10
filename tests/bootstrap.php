<?php
use Cake\Core\Configure;

require_once 'vendor/autoload.php';

if(!defined('DS')){
	define('DS', DIRECTORY_SEPARATOR);
}
define('ROOT', dirname(__DIR__) . DS);
define('APP', ROOT . 'tests' . DS . 'TestApp' . DS);
define('TMP', sys_get_temp_dir() . DS);
define('WEBROOT', APP . 'webroot' . DS);
define('WWW_ROOT', APP . 'webroot' . DS);

date_default_timezone_set('UTC');
mb_internal_encoding('UTF-8');

//Configure::write('debug', true);
Configure::write('App', [
    'namespace' => 'TestApp',
    'dir' => 'src'
]);
