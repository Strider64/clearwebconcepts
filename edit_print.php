<?php
// Include the configuration file and autoload file from Composer.
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";

use clearwebconcepts\{
    ErrorHandler,
    Database,
};
use Intervention\Image\ImageManagerStatic as Image;

$errorHandler = new ErrorHandler();
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();

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
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];

    $productImage = $_FILES['product_image'];
    if ($productImage['error'] == UPLOAD_ERR_OK) {
        $productImagePath = 'uploads/' . basename($productImage['name']);
        move_uploaded_file($productImage['tmp_name'], $productImagePath);

        $smallProductImagePath = 'uploads/small_' . basename($productImage['name']);
        resizeImage($productImagePath, $smallProductImagePath, 300, 300);

        $stmt = $pdo->prepare("UPDATE prints SET title = :title, product_image = :product_image, description = :description, price = :price, stock_quantity = :stock_quantity, larger_image = :larger_image WHERE id = :id");
        $stmt->bindParam(':product_image', $smallProductImagePath);
        $stmt->bindParam(':larger_image', $productImagePath);
    } else {
        $stmt = $pdo->prepare("UPDATE prints SET title = :title, description = :description, price = :price, stock_quantity = :stock_quantity WHERE id = :id");
    }

    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':stock_quantity', $stock_quantity);
    $stmt->bindParam(':id', $id);

    try {
        $stmt->execute();
        echo "Print updated successfully!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
