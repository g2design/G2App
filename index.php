<?php
$loader = require './vendor/autoload.php';

$app = G2Design\G2App::init($loader);

$app->add_modules('modules');

$app->defaultController("\\TestModule\\Controllers\\Index");

\G2Design\Config::load('config/config.json', true);

//Database connection
G2Design\Database::setup(\G2Design\Config::get()->databases);

try{
	$app->start();
} catch (Exception $ex) {
	echo $ex->getMessage();
}