<?php

require_once __DIR__ . "/../vendor/autoload.php";
$recordMonitor = new RecordMonitor();
$recordMonitor->createDBConnection();

$products = $recordMonitor->getAllProducts();
foreach ($products as $product) {
    $price = (string)$recordMonitor->getPriceById($product["id_from_url"]);
    if ($price != $product["price"]) {
        $clients = $recordMonitor->getSubscribedClient($product["id_from_url"]);
        foreach ($clients as $client) {
            AlertSender::send($client['name'], $client['email'], $client["id_from_url"], $price);
        }
    }
}


