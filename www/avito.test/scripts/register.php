<?php
require_once __DIR__ . "/../config/db_config.php";
require_once __DIR__ . "/../src/RecordMonitor.php";
require_once __DIR__ . "/../src/AlertSender.php";

$email = htmlspecialchars($_GET['email']);
$url   = htmlspecialchars($_GET['url']);

if (empty(trim($email)) === false &&
    empty(trim($url))   === false) {

    $record = new RecordMonitor(null, $email, $url);
    $record->save();

    $id_product = $record->getIdProductFromUrl();
    $price = $record->getPriceByUrl($url);
    $sender = new AlertSender();
    $sender->send($email, $id_product, $price, null, true);
}
echo json_encode(array("status" => true));

