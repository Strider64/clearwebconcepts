<?php
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
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <title>Edit Jigsaw Puzzle</title>
    <link rel="stylesheet" media="all" href="assets/css/stylesheet.css">
</head>
<body class="site">
<?php include 'assets/includes/inc-header-nav.php'; ?>
<main class="main_container">
    <form id="data_entry_form" class="checkStyle data-form" method="post" enctype="multipart/form-data">
        <input id="id" type="hidden" name="id"  value="">
        <div id="image_display_area">
            <img id="image_for_edited_record" src="" alt="">
        </div>
        <div id="file_grid_area">
            <input id="file" class="file-input-style" type="file" name="image">
            <label for="file">Select file</label>
        </div>
        <div id="title_grid_area">
            <label class="text_label_style" for="title">Title</label>
            <input id="title" class="" type="text" name="title" value="">
        </div>
        <div id="description_grid_area">
            <label class="text_label_style" for="description">Description</label>
            <textarea class="text_input_style" id="description" name="description"></textarea>
        </div>
        <label id="select_grid_difficulty_level_area">
            <select id="difficulty_level" class="select-css" name="difficulty_level">
                <option value="Easy">Easy</option>
                <option value="Medium">Medium</option>
                <option value="Hard">Hard</option>
                <option value="Expert">Expert</option>
            </select>
        </label>
        <label id="select_grid_category_area">
            <select id="category" class="select-css" name="category">
                <option value="general">General</option>
                <option value="halloween">Halloween</option>
                <option value="lego">LEGO</option>
                <option value="wildlife">Wildlife</option>
            </select>
        </label>
        <div id="submit_picture_grid_area">
            <button class="form-button" type="submit" name="submit" value="enter">Submit</button>
        </div>
    </form>
</main>
<aside class="sidebar">
    <div class="search-form-container">
        <form id="searchForm">
            <div class="input-group">
                <label for="searchTerm">Search:</label>
                <input type="text" placeholder="Search Content" id="searchTerm" class="input-field" autofocus required>
            </div>
            <button class="search_button" type="submit">Search</button>
        </form>
    </div>
</aside>

<footer class="colophon">
    <p>&copy; <?php echo date("Y") ?> Clear Web Concepts</p>
</footer>
<script src="assets/js/navigation.js"></script>
<script src="assets/js/edit_puzzle.js"></script>
</body>
</html>


