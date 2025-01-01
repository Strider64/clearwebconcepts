<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";
use Intervention\Image\ImageManagerStatic as Image;
// Import the ErrorHandler and Database classes from the clearwebconcepts namespace.
use clearwebconcepts\{
    ErrorHandler,
    Database,
    ImageContentManager,
    LoginRepository as Login
};

$errorHandler = new ErrorHandler();

// Register the exception handler method
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();
$checkStatus = new Login($pdo);

// To check for either 'member' or 'sysop'
if ($checkStatus->check_security_level(['sysop'])) {
    // Grant access
} else {
    // Access denied
    header('location: dashboard.php');
    exit();
}

function is_ajax_request(): bool
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
}


$save_result = false;

if (($_SERVER['REQUEST_METHOD'] === 'POST') && isset($_FILES['image'])) {
    $data = $_POST['cms'];
    $data['content'] = trim($data['content']);
    $errors = array();
    $exif_data = [];
    $file_name = $_FILES['image']['name']; // Temporary file:
    $file_size = $_FILES['image']['size'];
    $file_tmp = $_FILES['image']['tmp_name'];
    $thumb_tmp = $_FILES['image']['tmp_name'];
    $file_type = $_FILES['image']['type'];
    $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));


    /*
     * Set EXIF data info of image for database table that is
     * if it contains the info otherwise set to null.
     */
    if ($file_ext === 'jpeg' || $file_ext === 'jpg') {

        $exif_data = exif_read_data($file_tmp);


        if (array_key_exists('Make', $exif_data) && array_key_exists('Model', $exif_data)) {
            $data['Model'] = $exif_data['Make'] . ' ' . $exif_data['Model'];
        }

        if (array_key_exists('ExposureTime', $exif_data)) {
            $data['ExposureTime'] = $exif_data['ExposureTime'] . "s";
        }

        if (array_key_exists('ApertureFNumber', $exif_data['COMPUTED'])) {
            $data['Aperture'] = $exif_data['COMPUTED']['ApertureFNumber'];
        }

        if (array_key_exists('ISOSpeedRatings', $exif_data)) {
            $data['ISO'] = "ISO " . $exif_data['ISOSpeedRatings'];
        }

        if (array_key_exists('FocalLengthIn35mmFilm', $exif_data)) {
            $data['FocalLength'] = $exif_data['FocalLengthIn35mmFilm'] . "mm";
        }

    } else {
        $data['Model'] = null;
        $data['ExposureTime'] = null;
        $data['Aperture'] = null;
        $data['ISO'] = null;
        $data['FocalLength'] = null;
    }

    $data['content'] = trim($data['content']);

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
    $image_path = 'assets/image_path/img-entry-' . $image_random_string . '-2048x1365' . '.' . $file_ext;
    $thumb_path = 'assets/thumb_path/thumb-entry-' . $image_random_string . '-600x400' . '.' . $file_ext;


    move_uploaded_file($file_tmp, $image_path);
    move_uploaded_file($thumb_tmp, $thumb_path);


    // Load the image
    $image = Image::make($image_path);

    // Resize the image
    $image->resize(2048, 1365, function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
    });

    // Save the new image
    $image->save($image_path, 100);

    // Load the image with Intervention Image
    $image = Image::make($image_path);

    // Resize the image while maintaining the aspect ratio
    $image->resize(600, 400, function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
    });

    // Save the thumbnail
    $image->save($thumb_path, 100);


    $data['image_path'] = $image_path;
    $data['thumb_path'] = $thumb_path;


    /*
     * If no errors save ALL the information to the
     * database table.
     */
    if (empty($errors) === true) {
        // Save to Database Table CMS
        $timezone = new DateTimeZone('America/Detroit'); // Use your timezone here
        $today = new DateTime('now', $timezone);
        $data['date_updated'] = $data['date_added'] = $today->format("Y-m-d H:i:s");
        $cms = new ImageContentManager($pdo, $data);
        $result = $cms->create();

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
    <title>Insert Images</title>
    <link rel="stylesheet" media="all" href="assets/css/stylesheet.css">
</head>
<body class="site">
<?php include 'assets/includes/inc-header-nav.php'; ?>
<div class="main_container">
    <div class="home_article">
        <form id="data_entry_form" class="data-form checkStyle" method="post" enctype="multipart/form-data">
            <input type="hidden" name="cms[user_id]" value="<?= $_SESSION['user_id'] ?>">
            <input type="hidden" name="cms[author]" value="John Pepp>">
            <input type="hidden" name="cms[page]" value="cms">
            <input type="hidden" name="action" value="upload">
            <div id="progress_bar_container" class="progress-bar-container">
                <h2 id="progress_bar_title" class="progress-bar-title">Upload Progress</h2>
                <div id="progress_bar" class="progress-bar"></div>
            </div>
            <div id="file_grid_area">
                <input id="file" class="file-input-style" type="file" name="image">
                <label for="file">Select file</label>
            </div>
            <label id="select_grid_category_area">
                <select class="select-css" name="cms[category]">
                    <option disabled>Select a Category</option>
                    <option selected value="general">Home Page</option>
                    <option value="about">About</option>
                </select>
            </label>
            <div id="heading_heading_grid_area">
                <label class="heading_label_style" for="heading">Heading</label>
                <input class="enter_input_style" id="heading" type="text" name="cms[heading]" value="" tabindex="1"
                       required
                       autofocus>
            </div>
            <div id="sub_heading_grid_area">
                <label class="sub_heading_label_style" for="sub_heading">Sub Heading</label>
                <input class="enter_input_style" id="sub_heading" type="text" name="cms[sub_heading]" value="" tabindex="2"
            </div>
            <div id="content_style_grid_area">
                <label class="text_label_style" for="content">Content</label>
                <textarea class="text_input_style" id="content" name="cms[content]" tabindex="3"></textarea>
            </div>
            <div id="submit_picture_grid_area">
                <button class="form-button" type="submit" name="submit" value="enter">Submit</button>
            </div>
        </form>
    </div>
    <div class="home_sidebar">

    </div>

</div>
<aside class="sidebar">

</aside>
<footer class="colophon" itemprop="footer">
    <p>&copy; <?php echo date("Y") ?> Clear Web Concepts</p>
</footer>
<script src="assets/js/navigation.js"></script>
<script src="assets/js/upload_form_with_progress_bar.js"></script>
</body>
</html>
