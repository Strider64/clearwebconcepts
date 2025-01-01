<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";

use Intervention\Image\ImageManagerStatic as Image;
use clearwebconcepts\{
    ErrorHandler,
    Database,
    LoginRepository as Login,
    ImageContentManager
};

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

function is_ajax_request(): bool
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
}


$data = $_POST['data'];
if (($_SERVER['REQUEST_METHOD'] === 'POST') && isset($_FILES['image'])) {

    $errors = array();
    $exif_data = [];
    $file_name = $_FILES['image']['name']; // Temporary file:
    $file_size = $_FILES['image']['size'];
    $file_tmp = $_FILES['image']['tmp_name'];
    $file_type = $_FILES['image']['type'];
    $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));



    $data['description'] = trim($data['description']);

    $extensions = array("jpeg", "jpg", "png");

    if (in_array($file_ext, $extensions, true) === false) {
        $errors[] = "extension not allowed, please choose a JPEG or PNG file.";
    }

    if ($file_size >= 58720256) {
        $errors[] = 'File size must be less than or equal to 42 MB';
    }

    /*
     * Create unique name for image.
     */
    $image_random_string = bin2hex(random_bytes(16));
    $image_path = 'assets/puzzle_images/img-' . $image_random_string . '-600x400' . '.' . $file_ext;



    move_uploaded_file($file_tmp, $image_path);



    // Load the image
    $image = Image::make($image_path);



    // Resize the image
    $image->resize(600, 400, function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
    });

    // Sharpening the image
    $image->sharpen(25);

    // Save the new image
    $image->save($image_path, 100);


    // Database Table Column:
    $data['image_path'] = $image_path;



    /*
     * If no errors save ALL the information to the
     * database table.
     */
    if (empty($errors) === true) {
        // Save to Database Table CMS
        /* Initialize an array */
        $attribute_pairs = [];

        /*
         * Set up the query using prepared statements with named placeholders.
         */
        $sql = 'INSERT INTO puzzle_images (' . implode(", ", array_keys($data)) . ')';
        $sql .= ' VALUES ( :' . implode(', :', array_keys($data)) . ')';

        /*
         * Prepare the Database Table:
         */
        $stmt = $pdo->prepare($sql); // PHP Version 8.x Database::pdo()

        /*
         * Bind the corresponding values in order to
         * insert them into the table when the script
         * is executed.
         */
        foreach ($data as $key => $value) {
            if ($key === 'id') {
                continue; // Don't include the id
            }
            $stmt->bindValue(':' . $key, $value); // Bind values to the named placeholders
        }
        $result = $stmt->execute();

        if ($result) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success']);
            exit();
        }
    } else {
        if (is_ajax_request()) {
            // Send a JSON response with errors for AJAX requests
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'errors' => $errors]);
        }
    }

}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <title>Add Images to Jigsaw</title>
    <link rel="stylesheet" media="all" href="assets/css/stylesheet.css">
</head>
<body class="site">
<?php include 'assets/includes/inc-header-nav.php'; ?>
<main class="main_container">
    <form id="data_entry_form" class="checkStyle data-form" method="post" enctype="multipart/form-data">
        <div id="progress_bar_container" class="progress-bar-container">
            <h2 id="progress_bar_title" class="progress-bar-title">Upload Progress</h2>
            <div id="progress_bar" class="progress-bar"></div>
        </div>
        <div id="file_grid_area">
            <input id="file" class="file-input-style" type="file" name="image">
            <label for="file">Select file</label>
        </div>
        <div id="title_grid_area">
            <label for="title" class="text_label_style">Title</label>
            <input id="title" class="" type="text" name="data[title]" value="">
        </div>
        <div id="description_grid_area">
            <label class="text_label_style" for="description">Description</label>
            <textarea class="text_input_style" id="description" name="data[description]"></textarea>
        </div>
        <label id="select_grid_difficulty_level_area">
            <select class="select-css" name="data[difficulty_level]">
                <option disabled>Select Difficulty Level</option>
                <option selected value="Easy">Easy</option>
                <option value="Medium">Medium</option>
                <option value="Hard">Hard</option>
                <option value="Expert">Expert</option>
            </select>
        </label>
        <label id="select_grid_category_area">
            <select class="select-css" name="data[category]">
                <option disabled>Select a Category</option>
                <option value="lego">LEGO</option>
                <option value="wildlife" selected>Wildlife</option>
            </select>
        </label>
        <div id="submit_picture_grid_area">
            <button class="form-button" type="submit" name="submit" value="enter">Submit</button>
        </div>
    </form>
</main>
<aside class="sidebar">


</aside>

<footer class="colophon">
    <p>&copy; <?php echo date("Y") ?> Clear Web Concepts</p>
</footer>
<script src="assets/js/navigation.js"></script>
<script src="assets/js/upload_puzzle_form_with_progress_bar.js"></script>
</body>
</html>

