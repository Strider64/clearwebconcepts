<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";

// Import the ErrorHandler and Database classes from the clearwebconcepts namespace.
use clearwebconcepts\{
    ErrorHandler,
    Database
};

$errorHandler = new ErrorHandler();

// Register the exception handler method
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();

header('Content-Type: application/json');

// Get the product ID from the query string
$productId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$productId) {
    echo json_encode(['error' => 'Product ID is required']);
    exit;
}

try {
    // Prepare and execute a query to fetch the product by ID
    $stmt = $pdo->prepare('SELECT id, title, larger_image, description, price FROM prints WHERE id = :id LIMIT 1');
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the product was found
    if ($product) {
        echo json_encode($product);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
