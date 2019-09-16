<?php

namespace App;

require_once __DIR__ . '/App/Application.php';


$app = new Application();

$app->put('/', function(){
    echo 'hello!';
});

$app->get('/', function(){
    echo 'im GET!' . PHP_EOL;
});

$app->post('/', function(){
    echo 'im POST!' . PHP_EOL;
});

$app->run();

