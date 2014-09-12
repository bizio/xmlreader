<?php

if(getenv('APPLICATION_ENV') == 'development') {
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', true);
    ini_set('html_errors', true);
}

set_include_path(
    get_include_path() . PATH_SEPARATOR 
    . realpath(__DIR__) . '/../lib' . DIRECTORY_SEPARATOR);

define('APP_PATH', realpath(__DIR__) . DIRECTORY_SEPARATOR  
    . '..' . DIRECTORY_SEPARATOR . 'app');

require 'Application/App.php';

$config = array(
    'controllersPath' => APP_PATH . DIRECTORY_SEPARATOR . 'controllers',
    'viewsPath' => APP_PATH . DIRECTORY_SEPARATOR . 'views',
    'database' => array(
        'dsn' => 'mysql:dbname=xmldata;host=localhost',
        'user' => 'xmldatauser',
        'pass' => 'xmlD@t4us£r',
        'options' => array()
    )
);

$app = new Application\App($config);
$app->run();

?>
