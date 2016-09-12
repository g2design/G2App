<?php


include './vendor/autoload.php';

$app = G2Design\G2App::getInstance();
$app->add_route('hello/{any:i}?', function($any){
	echo "Hello $any";
});

$app->start();