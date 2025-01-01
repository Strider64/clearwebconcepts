<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";

$jsonString = file_get_contents('data.json');
$jsonData = json_decode($jsonString, true);

// Convert the PHP array back to a JSON string with pretty print
$prettyJsonData = json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);


// Import the ErrorHandler and Database classes from the clearwebconcepts namespace.
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
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add New Photography Print</title>
    <link rel="stylesheet" media="all" href="assets/css/stylesheet.css">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('addPrintForm').addEventListener('submit', function(event) {
                event.preventDefault();
                addPrint();
            });
        });

        function addPrint() {
            const formData = new FormData(document.getElementById('addPrintForm'));

            fetch('add_print.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('result').innerHTML = data;
                    document.getElementById('addPrintForm').reset();
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</head>
<body class="site" itemscope itemtype="http://schema.org/WebPage">
<?php include 'assets/includes/inc-header-nav.php'; ?>

<main class="main_container" itemprop="mainContentOfPage">

    <h1 class="add_print_header">Add New Photography Print</h1>

    <form class="add_print_form" id="addPrintForm" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label for="product_image">Product Image:</label>
            <input type="file" id="product_image" name="product_image" accept="image/*" required>
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>
        </div>

        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="stock_quantity">Stock Quantity:</label>
            <input type="number" id="stock_quantity" name="stock_quantity" required>
        </div>

        <button type="submit">Add Print</button>
    </form>

    <div id="result"></div>
</main>
<aside class="sidebar">


</aside>

<footer class="colophon" itemprop="footer">
    <p>&copy;<?php echo date("Y") ?> Clear Web Concepts LLC <a href="polices.php">Polices</a> </p>
</footer>
</body>
</html>