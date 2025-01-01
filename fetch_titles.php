<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";

use clearwebconcepts\ErrorHandler;
use clearwebconcepts\Database;

$errorHandler = new ErrorHandler();
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();

// Get the category from the GET parameter
$category = $_GET['category'] ?? null; // Replace with your actual category parameter

if (!$category) {
    echo json_encode(['error' => 'Category is required']);
    exit;
}

// Use a prepared statement to avoid SQL Injection
$stmt = $pdo->prepare('SELECT title FROM puzzle_images WHERE category = :category ORDER BY date_added DESC');
$stmt->bindParam(':category', $category, PDO::PARAM_STR);

$stmt->execute();
$titles = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($titles);
