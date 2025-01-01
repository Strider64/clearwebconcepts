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

// Instantiate the ErrorHandler class
$errorHandler = new ErrorHandler();

// Set the exception handler to use the handleException method from the ErrorHandler instance
set_exception_handler([$errorHandler, 'handleException']);

// Create a new instance of the Database class
$database = new Database();
// Create a PDO instance using the Database class's method
$pdo = $database->createPDO();

$login = new Login($pdo);
$checkStatus = new Login($pdo);

$cms = new ImageContentManager($pdo);

$displayFormat = ["gallery-container w-2 h-2", 'gallery-container w-2 h-2', 'gallery-container w-2 h-2', 'gallery-container h-2', 'gallery-container h-2', 'gallery-container w-2 h-2"', 'gallery-container h-2', 'gallery-container h-2', 'gallery-container w-2 h-2', 'gallery-container h-2', 'gallery-container h-2', 'gallery-container w-2 h-2'];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['category'])) {
        $category = $_GET['category'];
    } else {
        error_log('Category is not set in the GET data');
        $category = 'about';
    }
    $total_count = $cms->countAllPage($category);
} else {
    try {
        $category = 'general';
        $total_count = $cms->countAllPage($category);
    } catch (Exception $e) {
        error_log('Error while counting all pages: ' . $e->getMessage());
    }
}

/*
 * Using pagination in order to have a nice looking
 * website page.
 */

// Grab the current page the user is on
if (isset($_GET['page']) && !empty($_GET['page'])) {
    $current_page = urldecode($_GET['page']);
} else {
    $current_page = 1;
}

$per_page = 2; // Total number of records to be displayed:


// Grab Total Pages
$total_pages = $cms->total_pages($total_count, $per_page);


/* Grab the offset (page) location from using the offset method */
/* $per_page * ($current_page - 1) */
$offset = $cms->offset($per_page, $current_page);

// Figure out the Links that you want the display to look like
$links = new Links($current_page, $per_page, $total_count, $category);

// Finally grab the records that are actually going to be displayed on the page
$records = $cms->page($per_page, $offset, 'cms', $category);

?>
<!doctype html>
<html lang="en">
<head>
    <!-- Meta tags for responsiveness -->
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <!-- Title of the web page -->
    <title>About - John Pepp</title>


    <!-- Link to the external CSS file -->
    <link rel="stylesheet" media="all" href="assets/css/stylesheet.css">

</head>
<body class="site">
<?php include 'assets/includes/inc-header-nav.php'; ?>

<main class="main_container" itemprop="mainContentOfPage">
    <?php
    foreach ($records as $record) {
        $imagePath = htmlspecialchars($record['image_path'], ENT_QUOTES, 'UTF-8');
        $heading = htmlspecialchars($record['heading'], ENT_QUOTES, 'UTF-8');
        $content = htmlspecialchars($record['content'], ENT_QUOTES, 'UTF-8');

        echo '<div class="image-header">';
        echo '<img src="' . $imagePath . '" title="' . $heading . '" alt="' . $heading . '">';
        echo '</div>';
        echo '<h1>' . $heading . '</h1>';
        echo '<p>' . nl2br($content) . '</p>';
        echo '<br><hr><br>';
    }
    ?>
</main>

<aside class="sidebar">

    <?php echo $links->display_links(); ?>
</aside>

<footer class="colophon" itemprop="footer">
    <p>&copy;<?php echo date("Y") ?> Clear Web Concepts LLC <a href="polices.php">Polices</a> </p>
</footer>
<script src="assets/js/navigation.js"></script>

</body>
</html>
