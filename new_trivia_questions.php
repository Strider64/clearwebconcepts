<?php

require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";

use clearwebconcepts\{
    ErrorHandler,
    Database,
    LoginRepository as Login,
    TriviaDatabaseOBJ as Trivia
};

$errorHandler = new ErrorHandler();

// Register the exception handler method
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();
$login = new Login($pdo);
if (!$login->check_login_token()) {
    header('location: index.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quiz = $_POST['quiz'];
    $timezone = new DateTimeZone('America/Detroit'); // Use your timezone here
    $today = new DateTime('now', $timezone);
    $quiz['date_added'] = $today->format("Y-m-d H:i:s");
    $trivia = new Trivia($pdo, $quiz);
    $result = $trivia->create();
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <title>New Questions</title>
    <link rel="stylesheet" media="all" href="assets/css/admin.css">
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

<section class="main_container">
        <form id="add_to_db_table" class="data-form checkStyle" action="new_trivia_questions.php" method="post">
            <input type="hidden" name="quiz[user_id]" value="1">
            <div class="question_hidden">
                <select class="select-css" name="quiz[hidden]" tabindex="1">
                    <option value="yes">Hide Question: Yes</option>
                    <option value="no" selected>Hide Question: No</option>
                </select>
            </div>
            <div class="category_grid_area">
                <select id="category" class="select-css" name="quiz[category]" tabindex="2">
                    <option value="lego" selected>LEGO</option>
                    <option value="civics">Practice Civics Test</option>
                    <option value="photography">Photography</option>
                    <option value="movie">Movie</option>
                    <option value="space">Space</option>
                    <option value="sport">Sports</option>
                </select>
            </div>

            <div class="question_grid_area">
                <label for="question_style" for="content">Question</label>
                <textarea id="question_style" name="quiz[question]" tabindex="3"
                          placeholder="Add question here..."
                          autofocus></textarea>
            </div>
            <div class="answer_grid_area">
                <label>Answer 1</label>
                <input class="answer_style" id="addAnswer1" type="text" name="quiz[ans1]" value="" tabindex="4">
            </div>
            <div class="answer_grid_area">
                <label>Answer 2</label>
                <input class="answer_style" id="addAnswer2" type="text" name="quiz[ans2]" value="" tabindex="5">
            </div>

            <div class="answer_grid_area">
                <label>Answer 3</label>
                <input class="answer_style" id="addAnswer3" type="text" name="quiz[ans3]" value="" tabindex="6">
            </div>

            <div class="answer_grid_area">
                <label>Answer 4</label>
                <input class="answer_style" id="addAnswer4" type="text" name="quiz[ans4]" value="" tabindex="7">
            </div>

            <div class="answer_grid_area">
                <label>Correct Answer</label>
                <input class="answer_style" id="addCorrect" type="text" name="quiz[correct]" value=""
                       tabindex="8">
            </div>

            <div class="submit_grid_area">
                <button class="button_style" type="submit" name="submit" value="enter" tabindex="9">submit</button>
            </div>
        </form>
</section>

<aside class="sidebar">
    <?php $database->showAdminNavigation(); ?>
</aside>
<footer class="colophon">
    <p>&copy;<?php echo date("Y") ?> Clear Web Concepts LLC <a href="polices.php">Polices</a> </p>
</footer>
?>
</body>
</html>
