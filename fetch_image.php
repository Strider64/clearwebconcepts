<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";

use clearwebconcepts\ErrorHandler;
use clearwebconcepts\Database;

session_start();

$errorHandler = new ErrorHandler();
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();

$isInitialLoad = isset($_GET['isInitialLoad']) && $_GET['isInitialLoad'] == 'true';

if (!isset($_SESSION['shown_images']) || $isInitialLoad) {
    $_SESSION['shown_images'] = [];
}

$category = $_GET['category'] ?? null;
$title = $_GET['title'] ?? null;
$currentTitle = $_GET['currentTitle'] ?? null;

$executeParams = [];
$query = "SELECT image_path, description, title FROM puzzle_images WHERE 1";

if ($currentTitle) {
    $query .= " AND title > ?";
    $executeParams[] = $currentTitle;
}

// If it is not the initial load, consider shown_images in the query
if (!$isInitialLoad && count($_SESSION['shown_images']) > 0) {
    $placeholders = implode(",", array_fill(0, count($_SESSION['shown_images']), "?"));
    $query .= " AND image_path NOT IN ($placeholders)";
    $executeParams = array_merge($executeParams, $_SESSION['shown_images']);
}

if ($category) {
    $query .= " AND category = ?";
    $executeParams[] = $category;
}

if ($title) {
    $query .= " AND title = ?";
    $executeParams[] = $title;
}


$query .= " ORDER BY title LIMIT 1"; // Order by title and fetch the next one

$stmt = $pdo->prepare($query);
$stmt->execute($executeParams);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data) {
    // If it is not the initial load, add the fetched image to shown_images
    if (!$isInitialLoad) {
        $_SESSION['shown_images'][] = $data['image_path'];
    }
    echo json_encode($data);
} else {
    echo json_encode(['image_path' => 'NO_MORE_IMAGES', 'description' => 'Please Select an Image!']);
}
