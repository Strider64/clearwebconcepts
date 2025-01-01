<?php
// Include the configuration file and autoload file from Composer.
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";
use Intervention\Image\ImageManagerStatic as Image;
// Import the ErrorHandler and Database classes from the clearwebconcepts namespace.
use clearwebconcepts\{
    ErrorHandler,
    Database,
    Links,
    ImageContentManager,
    LoginRepository as Login
};
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$errorHandler = new ErrorHandler();

// Register the exception handler method
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();

$login = new Login($pdo);
// Check if the user is logged in and has the 'sysop' security level
if (!$login->check_login_token() || !$login->check_security_level(['sysop'])) {
    header('Location: index.php');
    exit();
}
// Function to resize image using Intervention Image
function resizeImage($sourcePath, $destPath, $width, $height): void
{
    $img = Image::make($sourcePath);
    $img->resize($width, $height, function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
    });
    $img->save($destPath);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];

    // Handle product image upload
    $productImage = $_FILES['product_image'];
    $productImagePath = 'uploads/' . basename($productImage['name']);
    move_uploaded_file($productImage['tmp_name'], $productImagePath);

    // Create a smaller version of the product image
    $smallProductImagePath = 'uploads/small_' . basename($productImage['name']);
    resizeImage($productImagePath, $smallProductImagePath, 300, 300);

    try {
        // Prepare the SQL statement
        $stmt = $pdo->prepare("INSERT INTO prints (title, product_image, description, price, stock_quantity, larger_image) VALUES (:title, :product_image, :description, :price, :stock_quantity, :larger_image)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':product_image', $smallProductImagePath); // Save the path to the smaller image
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock_quantity', $stock_quantity);
        $stmt->bindParam(':larger_image', $productImagePath); // Save the path to the original image

        // Execute the statement
        $stmt->execute();

        echo "New print added successfully!";
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

