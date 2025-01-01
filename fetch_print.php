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

$id = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM prints WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $print = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($print);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
