<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";

use clearwebconcepts\ErrorHandler;
use clearwebconcepts\Database;
use clearwebconcepts\LoginRepository as Login;

$errorHandler = new ErrorHandler();

// Register the exception handler method
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();

$login = new Login($pdo);
// Check if the user is logged in plus has the 'sysop' security level
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
    <title>Edit - Brain Wave Blitz</title>
    <link rel="stylesheet" media="all" href="assets/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="site">

<header class="nav">
    <!-- Input and label for the mobile navigation bar -->
    <input type="checkbox" class="nav-btn" id="nav-btn">
    <label for="nav-btn">
        <span></span>
        <span></span>
        <span></span>
    </label>

    <!-- Navigation links -->
    <nav class="nav-links" id="nav-links">
        <!-- Generating regular navigation links with a method from the Database class -->
        <?php $database->regular_navigation(); ?>
    </nav>

    <!-- Website name -->
    <div class="name-website">
        <h1 class="webtitle"></h1>
    </div>
</header>
<main class="main_container">
    <form id="data_entry_form" class="data-form checkStyle" action="edit_brainwaveblitz.php" method="post">
        <input id="current_id" type="hidden" name="id" value="">
        <div class="question_hidden">
            <select class="select-css" name="hidden" tabindex="1">

                <option value="yes">Hide Question: Yes</option>
                <option class="select_db" value=""  selected></option>
                <option value="no">Hide Question: No</option>
            </select>
        </div>
        <div class="category_grid_area">
            <select class="select-css" name="category" tabindex="2">
                <option id="category_selector" value=""></option>
                <option value="lego">LEGO</option>
                <option value="civics">Practice Civics Test</option>
                <option value="photography">Photography</option>
                <option value="movie">Movie</option>
                <option value="space">Space</option>
                <option value="sport">Sports</option>
            </select>
        </div>

        <div class="question_grid_area">
            <label for="question_style">Question</label>
            <textarea id="question_style" name="question" tabindex="3"
                      placeholder="Add question here..."
                      autofocus></textarea>
        </div>
        <div class="answer_grid_area">
            <label>Answer 1</label>
            <input class="answer_style" id="addAnswer1" type="text" name="ans1" value="" tabindex="4">
        </div>
        <div class="answer_grid_area">
            <label>Answer 2</label>
            <input class="answer_style" id="addAnswer2" type="text" name="ans2" value="" tabindex="5">
        </div>

        <div class="answer_grid_area">
            <label>Answer 3</label>
            <input class="answer_style" id="addAnswer3" type="text" name="ans3" value="" tabindex="6">
        </div>

        <div class="answer_grid_area">
            <label>Answer 4</label>
            <input class="answer_style" id="addAnswer4" type="text" name="ans4" value="" tabindex="7">
        </div>

        <div class="answer_grid_area">
            <label>Correct Answer</label>
            <input class="answer_style" id="addCorrect" type="text" name="correct" value=""
                   tabindex="8">
        </div>

        <div class="submit_grid_area">
            <button class="form-button" type="submit" name="submit" value="enter" tabindex="9">submit</button>
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
            <div class="input-group">
                <label for="select_id">Question:</label>
                <select class="select-css" id="select_id" name="id">
                    <option value="" disabled selected>Select question</option>
                </select>
            </div>
            <button class="search_button" type="submit">Search</button>
        </form>
    </div>
    <?php $database->showAdminNavigation(); ?>
    <div id="fb-login-btn" class="fb-login-button" data-width="" data-size="large" data-button-type="continue_with" data-layout="default" data-auto-logout-link="false" data-use-continue-as="true"></div>
    <button id="logout" style="display: none;">Logout</button>


</aside>
<footer class="colophon">
    <p>&copy;<?php echo date("Y") ?> Clear Web Concepts LLC <a href="polices.php">Polices</a> </p>
</footer>
<script src="assets/js/edit_brainwaveblitz.js"></script>
</body>
</html>