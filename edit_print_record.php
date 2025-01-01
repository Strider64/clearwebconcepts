<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";

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
// Check if the user is logged in and has the 'sysop' security level
if (!$login->check_login_token() || !$login->check_security_level(['sysop'])) {
    header('Location: index.php');
    exit();
}

$count = new ImageContentManager($pdo);
$total = $count->countAllGallery('images');

// New Instance of Login Class
$login = new Login($pdo);
$checkStatus = new Login($pdo);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Photography Print</title>
    <link rel="stylesheet" media="all" href="assets/css/stylesheet.css">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('selectPrintForm').addEventListener('submit', function(event) {
                event.preventDefault();
                fetchPrint();
            });

            document.getElementById('editPrintForm').addEventListener('submit', function(event) {
                event.preventDefault();
                updatePrint();
            });

            fetchPrintOptions();
        });

        function fetchPrintOptions() {
            fetch('fetch_prints.php')
                .then(response => response.json())
                .then(data => {
                    let printSelect = document.getElementById('printSelect');
                    data.forEach(print => {
                        let option = document.createElement('option');
                        option.value = print.id;
                        option.textContent = `ID: ${print.id} - ${print.title}`;
                        printSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error:', error));
        }

        function fetchPrint() {
            const printId = document.getElementById('printSelect').value;
            fetch(`fetch_print.php?id=${printId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_id').value = data.id;
                    document.getElementById('edit_title').value = data.title;
                    document.getElementById('edit_description').value = data.description;
                    document.getElementById('edit_price').value = data.price;
                    document.getElementById('edit_stock_quantity').value = data.stock_quantity;
                    document.getElementById('thumbnail').src = data.product_image;
                    document.getElementById('thumbnail').style.display = 'block';
                })
                .catch(error => console.error('Error:', error));
        }

        function updatePrint() {
            const formData = new FormData(document.getElementById('editPrintForm'));

            fetch('edit_print.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('result').innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</head>
<body class="site" itemscope itemtype="http://schema.org/WebPage">
<?php include 'assets/includes/inc-header-nav.php'; ?>

<main class="main_container" itemprop="mainContentOfPage">
    <h1 class="edit_print_header">Edit Photography Print</h1>

    <form id="selectPrintForm" class="centered_form">
        <div class="form-group">
            <label for="printSelect">Select Print:</label>
            <select id="printSelect" name="printSelect" required>
                <option value="">-- Select a Print --</option>
            </select>
        </div>
        <button type="submit">Load Print</button>
    </form>

    <div class="thumbnail_container">
        <img id="thumbnail" src="" alt="Current Product Image" style="display:none; max-width: 200px; margin: 1em 0;">
    </div>

    <form class="edit_print_form" id="editPrintForm" enctype="multipart/form-data">
        <input type="hidden" id="edit_id" name="id">
        <div class="form-group">
            <label for="edit_title">Title:</label>
            <input type="text" id="edit_title" name="title" required>
        </div>

        <div class="form-group">
            <label for="edit_description">Description:</label>
            <textarea id="edit_description" name="description" required></textarea>
        </div>

        <div class="form-group">
            <label for="edit_price">Price:</label>
            <input type="number" id="edit_price" name="price" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="edit_stock_quantity">Stock Quantity:</label>
            <input type="number" id="edit_stock_quantity" name="stock_quantity" required>
        </div>

        <div class="form-group">
            <label for="edit_product_image">Product Image:</label>
            <input type="file" id="edit_product_image" name="product_image" accept="image/*">
        </div>

        <button type="submit">Update Print</button>
    </form>

    <div id="result"></div>
</main>
<aside class="sidebar"></aside>

<footer class="colophon" itemprop="footer">
    <p>&copy;<?php echo date("Y") ?> Clear Web Concepts LLC <a href="polices.php">Polices</a></p>
</footer>
</body>
</html>
