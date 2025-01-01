<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";

// Import the ErrorHandler and Database classes from the PhotoTech namespace.
use clearwebconcepts\{
    ErrorHandler,
    Database,
    Links,
    ImageContentManager as CMS,
    LoginRepository as Login
};

// Instantiate the ErrorHandler class
$errorHandler = new ErrorHandler();
set_exception_handler([$errorHandler, 'handleException']);

// Create a new instance of the Database class
$database = new Database();
$pdo = $database->createPDO();

$login = new Login($pdo);

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and has the 'sysop' security level
if (!$login->check_login_token() || !$login->check_security_level(['sysop'])) {
    header('Location: index.php');
    exit();
}
$cms = new CMS($pdo);

$records = $cms->headings();


?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <title>Edit Page</title>
    <link rel="stylesheet" media="all" href="assets/css/stylesheet.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="site">

<?php include 'assets/includes/inc-header-nav.php'; ?>
<main class="main_container">

    <form id="data_entry_form" class="data-form checkStyle" action="edit_cms.php" method="post" enctype="multipart/form-data">

        <input id="id" type="hidden" name="id" value="">
        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
        <input type="hidden" name="author" value="John Pepp>">
        <input type="hidden" name="page" value="gallery">
        <input type="hidden" name="action" value="upload">
        <div id="image_display_area">
            <img id="image_for_edited_record" src="" alt="">
        </div>
        <div id="file_grid_area">
            <input id="file" class="file-input-style" type="file" name="image">
            <label for="file">Select file</label>
        </div>
        <label id="select_grid_category_area">
            <select class="select-css" name="category">
                <option id="category" value=""></option>
                <option value="general">Home Page</option>
                <option value="about">About</option>
            </select>
        </label>
        <div id="heading_heading_grid_area">
            <label class="heading_label_style" for="heading">Heading</label>
            <input class="heading" class="enter_input_style" type="text" name="heading" value="" tabindex="1" required>
        </div>
        <div id="content_style_grid_area">
            <label class="text_label_style" for="content">Content</label>
            <textarea class="text_input_style table_height" id="content" name="content" tabindex="2"></textarea>
        </div>
        <div id="submit_picture_grid_area">
            <button class="form-button" type="submit" name="submit" value="enter">submit</button>
        </div>
    </form>

</main>
<aside class="sidebar">
    <div class="search-form-container">
        <form id="searchForm">
            <div class="input-group">
                <label for="heading">Heading:</label>
                <select class="select-css" id="heading" name="heading">
                    <option value="" disabled selected>Select Heading</option>
                    <?php
                    foreach ($records as $record) {
                        echo '<option value="' . $record['heading'] . '">' . $record['heading'] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="input-group">
                <label for="searchTerm">Search Product Content:</label>
                <input type="text" placeholder="Search Content" id="searchTerm" class="input-field" autofocus required>
            </div>
            <button class="search_button" type="submit">Search</button>
        </form>
    </div>
</aside>
<footer class="colophon" itemprop="footer">
    <p>&copy; <?php echo date("Y") ?> Clear Web Concepts LLC</p>
</footer>
<script src="assets/js/navigation.js"></script>
<script src="assets/js/edit_cms.js"></script>
</body>
</html>
