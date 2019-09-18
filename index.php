<?php

namespace App;

require_once __DIR__ . '/config/db_cfg.php';
require_once __DIR__ . '/App/Application.php';
require_once __DIR__ . '/App/DB.php';

$app = new Application();

$app->get('/users/{user_id}/services/{service_id}/tarifs', function ($data, $params) {
    $userId = $params['user_id'];
    $serviceId = $params['service_id'];

    $db = new DB(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);

    $userTarif = $db->getUserTarif($userId, $serviceId);

    header('Content-type: application/json');
    if (empty($userTarif)) {
        header('HTTP/1.1 404 Not Found');
        return json_encode(['result' => 'error', 'message' => 'not data found for this parameters']);
    }

    $availableTarifs = $db->getAvailableTarifs($userId, $serviceId, true);

    $result = [
        'result' => 'ok',
        'title' => $userTarif['title'],
        'link' => $userTarif['link'],
        'speed' => $userTarif['speed'],
        'tarifs' => $availableTarifs
    ];

    echo json_encode($result);
});


$app->put('/users/{user_id}/services/{service_id}/tarif', function ($data, $params) {

    $decodedData = json_decode($data, true);
    if (empty($decodedData)) {
        header('HTTP/1.1 204 No Content');
        return json_encode(['result' => 'error']);
    }
    $tarifId = $decodedData['tarif_id'];
    $userId = $params['user_id'];
    $serviceId = $params['service_id'];

    $db = new DB(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);

    $availableTarifs =  $db->getAvailableTarifs($userId, $serviceId, false);
//var_dump($availableTarifs); die;

    $tarifsToSet = array_filter($availableTarifs, function($item) use ($tarifId) {
        return $tarifId == $item['ID'] ;
    });

    header('Content-type: application/json');

    if (empty($tarifsToSet)) {
        header('HTTP/1.1 404 Not Found');
        return json_encode(['result' => 'error']);
    }

    $db->setServiceTarifs($serviceId, $tarifsToSet);
    echo json_encode(['result' => 'ok']);
});


$app->run();
