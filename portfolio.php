<?php
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

$errorHandler = new ErrorHandler();

// Register the exception handler method
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();

$count = new ImageContentManager($pdo);

$total = $count->countAllGallery('images');
//echo "<pre>" . print_r($total, 1) . "</pre>";
//exit();
// New Instance of Login Class
$login = new Login($pdo);
$checkStatus = new Login($pdo);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Portfolio</title>
    <style>
        .sidebar_pages {
            display: flex;
            -webkit-flex-wrap: wrap;
            flex-wrap: wrap;
            justify-content: flex-start;
            height: auto;
            width: 100%;
        }
    </style>
    <link rel="stylesheet" media="all" href="assets/css/stylesheet.css">
    <script type="application/ld+json">

    </script>


</head>
<body class="site" itemscope itemtype="http://schema.org/WebPage">
<?php include 'assets/includes/inc-header-nav.php'; ?>

<main class="main_manager_container">


</main>


<aside class="sidebar">
    <form id="gallery_category" action="portfolio.php" method="post">
        <label for="category">Category:</label>
        <select id="category" class="select-css" name="category" tabindex="1">
            <option value="images">Images</option>
        </select>
    </form>
    <div class="sidebar_pages">

    </div>
</aside>


<footer class="colophon" itemprop="footer">
    <p>&copy;<?php echo date("Y") ?> Clear Web Concepts LLC <a href="polices.php">Polices</a> </p>
</footer>

<div class="lightbox"></div>
<script src="assets/js/navigation.js"></script>
<script src="assets/js/portfolioManager.js"></script>
</body>
</html>
