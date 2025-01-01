<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";

$jsonString = file_get_contents('data.json');
$jsonData = json_decode($jsonString, true);
// Convert the PHP array back to a JSON string with pretty print
$prettyJsonData = json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

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

$checkStatus = new Login($pdo);

// To check for either 'member' or 'sysop'
if ($checkStatus->check_security_level(['member', 'sysop'])) {
    // Grant access
} else {
    // Access denied
    header('location: index.php');
    exit();
}
$cms = new ImageContentManager($pdo);

$displayFormat = ["gallery-container w-2 h-2", 'gallery-container w-2 h-2', 'gallery-container w-2 h-2', 'gallery-container h-2', 'gallery-container h-2', 'gallery-container w-2 h-2"', 'gallery-container h-2', 'gallery-container h-2', 'gallery-container w-2 h-2', 'gallery-container h-2', 'gallery-container h-2', 'gallery-container w-2 h-2'];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['category'])) {
        $category = $_GET['category'];
    } else {
        error_log('Category is not set in the GET data');
        $category = 'general';
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <title>Dashboard for Members</title>
    <link rel="stylesheet" media="all" href="assets/css/stylesheet.css">
    <style>

        #myButton {
            outline: none;
            color: #fff;
            border: none;
            background-color: #f12929;
            box-shadow: 2px 2px 1px rgba(0, 0, 0, 0.5);
            width: 6.25em;
            font-family: "Rubik", sans-serif;
            font-size: 1.2em;
            text-transform: capitalize;
            text-decoration: none;
            cursor: pointer;
            padding: 0.313em;
            margin: 0.625em;
            transition: background-color 0.5s;
            float: right;
            text-align: center;
        }

        #myButton:hover {
            background-color: #009578;
        }

        .pagination {
            display: inline-block;
            padding-left: 0;
            margin: 20px 0;
            border-radius: 4px;
        }

        .pagination > li {
            display: inline;
        }

        .pagination > li > a,
        .pagination > li > span {
            position: relative;
            float: left;
            font-size: 1.0em;
            padding: 6px 12px;
            margin-left: -1px;
            line-height: 1.42857143;
            color: #337ab7;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #ddd;
        }

        .pagination > li:first-child > a,
        .pagination > li:first-child > span {
            margin-left: 0;
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }

        .pagination > li:last-child > a,
        .pagination > li:last-child > span {
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }

        .pagination > li > a:hover,
        .pagination > li > a:focus {
            color: #23527c;
            background-color: #eee;
            border-color: #ddd;
        }

        .pagination > .active > a,
        .pagination > .active > span,
        .pagination > .active > a:hover,
        .pagination > .active > span:hover,
        .pagination > .active > a:focus,
        .pagination > .active > span:focus {
            z-index: 2;
            color: #fff;
            cursor: default;
            background-color: #337ab7;
            border-color: #337ab7;
        }

        .pagination > li > span {
            display: inline-block;
            padding: 6px 12px;
            color: #999;
            background-color: #fff;
            border: 1px solid #ddd;
            box-sizing: border-box;
            height: 2.313em;
        }

        .pagination > li > span::before {
            content: '...';
            display: inline-block;
            vertical-align: middle;
        }


    </style>
    <script type="application/ld+json">
        <?php echo $prettyJsonData; ?>

    </script>

</head>
<body class="site">
<?php include 'assets/includes/inc-header-nav.php'; ?>

<main class="main_container">
    <?php
    foreach ($records as $record) {
        if ($checkStatus->check_security_level(['sysop'])) {
            echo '<a id="myButton" href="delete.php?id=' . $record['id'] . '" class="delete-link" onclick="return confirmDelete();">Delete</a>';
        }
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
    <?php
    // To check for either 'member' or 'sysop'
    if ($checkStatus->check_security_level(['sysop'])) {
        $database->showAdminNavigation();
    }
    ?>
</aside>

<footer class="colophon" itemprop="footer">
    <p>&copy;<?php echo date("Y") ?> Clear Web Concepts</p>
</footer>
<script src="assets/js/navigation.js"></script>
<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this record?');
    }
</script>
</body>
</html>
