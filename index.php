<?php

namespace App;

require_once __DIR__ . '/config/db_cfg.php';
require_once __DIR__ . '/App/Application.php';
require_once __DIR__ . '/App/DB.php';

$app = new Application();

$app->put('/users/{user_id}/services/{service_id}/tarif', function($data, $params){
    $userId = $params['user_id'];
    $serviceId = $params['service_id'];
    //return var_export($params);
    $db = new DB(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);

    $userTarif = $db->getUserTarif($)
    $rows = $db->getAvailableTarifs($params['user_id']);
    return var_export($rows);

});

/*
$app->get('/users/{user_id}/services/{service_id}/tarifs', function($data, $params){
    echo 'im GET!' . PHP_EOL;
});

$app->post('/', function(){
    echo 'im POST!' . PHP_EOL;
});
*/

$app->run();

