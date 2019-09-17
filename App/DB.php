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

    public function getUserTarif($userId)
    {
        $stmt = $this->pdo->prepare('select tarifs.* from services inner join tarifs
            on tarifs.ID= services.tarif_id where user_id = ?');
        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    function getAvailableTarifs($userId)
    {
        $stmt = $this->pdo->prepare('select * from tarifs
          where tarif_group_id =
              (select tarif_group_id from tarifs where ID =
                  (select tarif_id from services where user_id = ?))');

        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}