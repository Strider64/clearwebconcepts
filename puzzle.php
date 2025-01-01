<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/config.php';
require_once "vendor/autoload.php";

/*
 * Jigsaw Puzzle 2.0 βeta
 * Created by John Pepp
 * on August 16, 2023
 * Updated by John Pepp
 * on September 23, 2023
 */

// Import the ErrorHandler and Database classes from the PhotoTech namespace.
use clearwebconcepts\{
    ErrorHandler,
    Database,
    Links,
    ImageContentManager,
    LoginRepository as Login
};

$_SESSION['shown_images'] = []; // Start the game over if web browser is refreshed

// Instantiate the ErrorHandler class
$errorHandler = new ErrorHandler();

// Set the exception handler to use the handleException method from the ErrorHandler instance
set_exception_handler([$errorHandler, 'handleException']);

// Create a new instance of the Database class
$database = new Database();
// Create a PDO instance using the Database class's method
$pdo = $database->createPDO();
$checkStatus = new Login($pdo);
?>
<!doctype html>
<html lang="en" itemscope itemtype="http://schema.org/WebPage">
<head>
    <!-- Meta tags for responsiveness -->
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <title>Connect a Piece</title>
    <link rel="stylesheet" media="all" href="assets/css/stylesheet.css">


</head>
<body class="site">
<header class="headerStyle" itemprop="header">
    <div class="logo">
        <a class="logoImage" href="portfolio.php" title="Portfolio"><img src="assets/images/img-company-logo-new-001.jpg" alt="clearwebconcepts"></a>
    </div>
    <div class="header-text"></div>

</header>
<nav class="nav">
    <!-- Burger Button for mobile navigation -->
    <button class="nav-btn" id="nav-btn" aria-label="Toggle navigation">
        <span></span>
        <span></span>
        <span></span>
    </button>
    <!-- Navigation links -->
    <div class="nav-links" id="nav-links">
        <?php $database->regular_navigation(); ?>

    </div>
</nav>
<main class="main_container" itemprop="mainContentOfPage">
    <canvas id="puzzleCanvas" class="puzzle-canvas" width="900" height="700"></canvas>
    <div id="customAlertOverlay" class="custom-alert-overlay">
        <div id="customAlert" class="custom-alert">
            <div id="customAlertContent" class="custom-alert-content">
                <p id="alertText">Your custom alert text will appear here.</p>
            </div>
        </div>
    </div>
</main>
<aside class="sidebar">
    <div class="categorySelection">
        <label for="category">Choose a category:</label>
        <select id="category" name="category">
            <option value="wildlife" selected>Wildlife</option>
            <option value="lego">LEGO</option>
        </select>
    </div>
    <div class="titleSelection">
        <label for="title">Choose Puzzle</label>
        <select id="title" name="title">

        </select>
    </div>
    <div class="puzzleImage">
        <img src="" id="puzzleImage" alt="Puzzle Image">
        <p class="imageDescription">Text</p>
    </div>


</aside>

<footer class="colophon" itemprop="footer">
    <p>&copy;<?php echo date("Y") ?> Clear Web Concepts LLC <a href="polices.php">Polices</a> </p>
</footer>
<script src="assets/js/navigation.js"></script>
<script>
    if (window.innerWidth <= 768) {
        window.location.href = "https://www.brainwaveblitz.com/index.php";
    }
</script>
<script src="assets/js/puzzle_script.js"></script>
</body>
</html>

