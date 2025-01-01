<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";

// Import the ErrorHandler and Database classes from the PhotoTech namespace.
use clearwebconcepts\{
    ErrorHandler,
    Database,
    Links,
    ImageContentManager,
    LoginRepository as Login
};


$errorHandler = new ErrorHandler();
$database = new Database();

$pdo = $database->createPDO();

$loginRepository = new Login($pdo);

$loginRepository->logoff();