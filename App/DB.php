<?php

namespace App;

use PDO;

class DB
{
    public $pdo;

    public function __construct($dbhost, $dbname, $dbuser, $dbpass)
    {
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->pdo = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass, $opt);
    }

    public function getUserTarif($userId, $serviceId)
    {
        $stmt = $this->pdo->prepare('select tarifs.* from services inner join tarifs
            on tarifs.ID= services.tarif_id where user_id = ? and services.ID = ?');

        $stmt->execute([$userId, $serviceId]);

        $userTarifs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return empty($userTarifs) ? [] : $userTarifs[0];
    }

    public function getAvailableTarifs($userId, $serviceId, $modifyFields = false)
    {
        $stmt = $this->pdo->prepare("select ID, title, price, pay_period, speed
         from tarifs where tarif_group_id IN
          (select tarif_group_id from tarifs where tarifs.ID IN
            (select tarif_id from services where user_id = ? AND services.ID = ?))");

        $stmt->execute([$userId, $serviceId]);
        $tarifs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $modifyFields ? array_map([$this, 'modifyTarifFields'], $tarifs) : $tarifs;
    }

    private function modifyTarifFields(array $tarif)
    {
        $tarif['price'] = (int)$tarif['price'];

        $months = $tarif['pay_period'];
        $tarif['pay_period'] = (string)$months;

        $payTime = strtotime("today midnight +$months months");
        $tarif['new_payday'] = $payTime . date('O', $payTime);

        return $tarif;
    }

    public function setServiceTarifs($serviceId, $tarifsToSet)
    {
        $stmt = $this->pdo->prepare("UPDATE services SET tarif_id = ?, payday = ? WHERE ID = ?");

        foreach ($tarifsToSet as $tarif) {
            $payDayTimeStamp = strtotime("today midnight +{$tarif['pay_period']} months");
            $payday = date('Y-m-d', $payDayTimeStamp);

            $stmt->execute([$tarif['ID'], $payday, $serviceId]);
        }
    }

}