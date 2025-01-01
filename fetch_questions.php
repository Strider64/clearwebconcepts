<?php
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";

use clearwebconcepts\ErrorHandler;
use clearwebconcepts\Database;
use clearwebconcepts\TriviaDatabaseOBJ as Trivia;

$errorHandler = new ErrorHandler();

// Register the exception handler method
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();

$trivia = new Trivia($pdo);

$category = filter_var($_GET['category'], FILTER_SANITIZE_STRING);
if (empty($category)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid category']);
    exit;
}

$data = $trivia->fetchQuestions($category);

output($data);

function output($output): void
{
    http_response_code(200);
    try {
        echo json_encode($output, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
    }
}
