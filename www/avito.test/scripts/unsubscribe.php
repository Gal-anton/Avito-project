<?php
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config/db_config.php";

if (isset($_GET["email"]) === true &&
    !empty(trim($_GET["email"]))) {
        $recordMonitor = new RecordMonitor();
        $recordMonitor->createDBConnection();
        $recordMonitor->deleteClient($_GET["email"]);
        echo "Вы успешно отписались от подписки";
} else {
    echo "Операция не выполнена";
}