<?php
// edit_update_blog.php

header('Content-Type: application/json');
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
use Intervention\Image\ImageManagerStatic as Image;
$thumb_width = 600;
$thumb_height = 400;

$errorHandler = new ErrorHandler();

// Register the exception handler method
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();
$login = new Login($pdo);

try {
    // Check if the required form data is provided
    if (!isset($_POST['id']) || !isset($_POST['heading']) || !isset($_POST['content'])) {
        throw new Exception("Missing required form data.");
    }

    // Get form data
    $id = (int) $_POST['id'];
    $category = $_POST['category'];
    $heading= $_POST['heading'];
    $content = $_POST['content'];
    $timezone = new DateTimeZone('America/Detroit'); // Use your timezone here
    $today = new DateTime('now', $timezone);
    $date_updated = $today->format("Y-m-d H:i:s");

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $image = $_FILES['image'];
        $destinationDirectory = 'assets/image_path/';
        $saveDirectory = 'assets/image_path/';

        $destinationFilename = time() . '_' . str_replace(' ', '_', basename($image['name']));
        $destinationPath = $destinationDirectory . $destinationFilename;
        $target_file = $destinationPath;

        // Load the image
        $loadedImage = Image::make($image['tmp_name']);

        // Resize the image
        $loadedImage->resize(2048, 1365, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Save the new image
        $loadedImage->save($destinationPath, 100);



        // Load the image with Intervention Image
        $image = Image::make($destinationPath);

        // Resize the image while maintaining the aspect ratio
        $image->resize($thumb_width, $thumb_height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Save the thumbnail
        $thumb_path = 'assets/thumb_path/thumb-path-' . time() . $thumb_width . 'x' . $thumb_height . '.' . pathinfo($destinationFilename, PATHINFO_EXTENSION);
        $image->save($thumb_path, 100);

        // Retrieve the current image file path from the database
        $current_image_query = "SELECT image_path FROM portfolio WHERE id = :id";
        $current_image_stmt = $pdo->prepare($current_image_query);
        $current_image_stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $current_image_stmt->execute();
        $current_image_row = $current_image_stmt->fetch(PDO::FETCH_ASSOC);
        $current_image_path = $current_image_row['image_path'];

        // Delete the old image if it exists
        $current_image_abs_path = $_SERVER['DOCUMENT_ROOT'] . $current_image_path;
        if (file_exists($current_image_abs_path)) {
            unlink($current_image_abs_path);
        }

        // Prepare the SQL query with placeholders
        $sql = "UPDATE portfolio SET category = :category, heading = :heading, content = :content, image_path = :image_path, thumb_path = :thumb_path, date_updated = :date_updated WHERE id = :id";
        $stmt = $pdo->prepare($sql);

        // Bind the values to the placeholders
        $savePath = $saveDirectory . $destinationFilename;
        $stmt->bindParam(':image_path', $savePath);
        $stmt->bindParam(':thumb_path', $thumb_path);

    } else {
        // Prepare the SQL query with placeholders
        $sql = "UPDATE portfolio SET category = :category, heading = :heading, content = :content, date_updated = :date_updated WHERE id = :id";
        $stmt = $pdo->prepare($sql);
    }

    // Bind the values to the placeholders
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':heading', $heading);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam('date_updated', $date_updated);

    // Execute the prepared statement
    $stmt->execute();

    // Check if the update was successful
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Record updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No record updated.']);
    }
} catch (PDOException $e) {
    echo json_encode(['PDO error' => $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

