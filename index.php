<?php

global $loader;
$loader = require './vendor/autoload.php';

$app = G2Design\G2App::getInstance();

$app->add_modules('test');

$app->defaultController("\\TestModule\\Controllers\\Index");

$app->start();