<?php
require_once __DIR__ . "/../config/db_config.php";
require_once __DIR__ . "/../src/RecordMonitor.php";
require_once __DIR__ . "/../src/AlertSender.php";

$email = (isset($_GET['email']) === true) ? htmlspecialchars($_GET['email']) : "";
$url   = (isset($_GET['url']) === true) ? htmlspecialchars($_GET['url']) : "";

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

