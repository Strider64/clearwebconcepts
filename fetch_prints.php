<?php
// Include the configuration file and autoload file from Composer.
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";

use clearwebconcepts\{
    ErrorHandler,
    Database,
};

$errorHandler = new ErrorHandler();
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();

try {
    $stmt = $pdo->prepare("SELECT id, title FROM prints");
    $stmt->execute();
    $prints = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($prints);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
