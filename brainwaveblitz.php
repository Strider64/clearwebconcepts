<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";

// Import the ErrorHandler and Database classes from the PhotoTech namespace.
use clearwebconcepts\Database;
use clearwebconcepts\ErrorHandler;


/*
 * Brainwave Blitz 1.0 βeta
 * Created by John Pepp
 * on June 30, 2023
 * Updated by John Pepp
 * on July 4, 2023
 */

// Instantiate the ErrorHandler class
$errorHandler = new ErrorHandler();

// Set the exception handler to use the handleException method from the ErrorHandler instance
set_exception_handler([$errorHandler, 'handleException']);

// Create a new instance of the Database class
$database = new Database();
// Create a PDO instance using the Database class's method
$pdo = $database->createPDO();
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Meta tags for responsiveness -->
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <!-- Title of the web page -->
    <title>Brainwave Blitz</title>
    <!-- Link to the external CSS file -->
    <link rel="stylesheet" media="all" href="assets/css/trivia.css">


</head>
<body>

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
<main id="content" class="main">
    <div class="image-header">
        <img src="assets/images/img-brainwave-header.jpg" alt="Brain Wave Blitz">
    </div>
    <div id="quiz" class="displayMessage">
        <!-- Main game section, initially hidden -->
        <div id="mainGame" style="display: none;">
            <!-- Section for displaying the question and answers -->
            <div id="triviaSection">
                <div id="questionBox">
                    <!-- Current question and score information -->
                    <div id="current" class="info-bar">
                        <p>Current question is <span id="currentQuestion" data-record=""></span></p>
                        <p>Your score: <span id="score">0</span></p>
                    </div>
                    <h2 id="question">Question Goes Here!</h2>
                    <div id="answers">
                        <button class="buttonStyle" id="ans1"></button>
                        <button class="buttonStyle" id="ans2"></button>
                        <button class="buttonStyle" id="ans3"></button>
                        <button class="buttonStyle" id="ans4"></button>
                    </div>
                    <!-- Area for showing whether the answer was correct or not -->
                    <p id="result"></p>
                </div>
                <!-- Next button for moving to the next question -->
                <button id="next" class="nextBtn">Next</button>
            </div>
        </div>
    </div>

    <!-- Selector for choosing the category of questions -->
    <div id="categorySelector">
        <label for="category">Choose a category:</label>
        <select id="category" name="category">
            <option value="">--Please choose a category--</option>
            <option value="lego">LEGO</option>
            <option value="civics">Practice Civics Test</option>
            <option value="photography">Photography</option>
            <option value="space">Space</option>
            <option value="movie">Movies</option>
            <option value="sport">Sports</option>
        </select>
    </div>
</main>

<!-- Link to the external JavaScript file -->
<script type="module" src="assets/js/main.js"></script>
</body>
</html>
