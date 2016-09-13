<?php

global $loader;
$loader = require './vendor/autoload.php';

$app = G2Design\G2App::getInstance();

$app->add_modules('modules');

$app->defaultController("\\TestModule\\Controllers\\Index");

\G2Design\Config::load('config/config.json', true);

//Database connection
G2Design\Database::setup(\G2Design\Config::get()->databases);


$app->start();