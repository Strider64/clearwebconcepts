<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";

use Intervention\Image\ImageManagerStatic as Image;

// Import the ErrorHandler and Database classes from the PhotoTech namespace.
use clearwebconcepts\{
    ErrorHandler,
    Database,
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
if ($checkStatus->check_security_level(['sysop'])) {
    // Grant access
} else {
    // Access denied
    header('location: dashboard.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <title>Service Information Form</title>
    <link rel="stylesheet" media="all" href="assets/css/stylesheet.css">
    <script src="assets/js/fetch_request.js"></script>
    <script src="assets/js/form_handler.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new FormHandler('serviceForm', 'saveData.php');
        });
    </script>
</head>
<body class="site">
<?php include 'assets/includes/inc-header-nav.php'; ?>
<main class="main_container">
    <form id="serviceForm" class="service-data-form">
        <label for="name">Company Name:</label>
        <input type="text" id="name" name="name"><br><br>

        <fieldset>
            <legend>Address:</legend>
            <label for="locality">Locality:</label>
            <input type="text" id="locality" name="locality" placeholder="Full Address or General Location"><br><br>
            <label for="region">Region:</label>
            <input type="text" id="region" name="region" placeholder="Where You Do Business"><br><br>
            <label for="postalCode">Postal Code:</label>
            <input type="text" id="postalCode" name="postalCode"><br><br>
            <label for="country">Country:</label>
            <input type="text" id="country" name="country"><br><br>
        </fieldset>
        <br><br>

        <label for="telephone">Telephone:</label>
        <input type="tel" id="telephone" name="telephone"><br><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email"><br><br>
        <label for="url">Website's URL</label>
        <input type="url" id="url" name="url" placeholder="https://www.sample.com/"><br><br>
        <label for="openingHours"></label>
        <input type="text" id="openingHours" name="openingHours" placeholder="Hours Open - Example: Mo, Tu, We, Th, Fr 09:00-17:00"><br><br>
        <label for="description">Description:</label>
        <input type="text" id="description" name="description" placeholder="A Short Description"><br><br>
        <fieldset>
            <legend>Founder:</legend>
            <label for="founderName">Name:</label>
            <input type="text" id="founderName" name="founderName"><br><br>
        </fieldset>

        <fieldset>
            <legend>Same As (URLs):</legend>
            <textarea id="sameAs" name="sameAs" rows="4" cols="50" placeholder="Enter URLs here, separated by new lines or commas"></textarea>
        </fieldset>
        <div class="displaySuccess"></div>
        <input type="submit" value="Submit">
    </form>

</main>
<aside class="sidebar">

</aside>
<footer class="colophon" itemprop="footer">
    <p>&copy; <?php echo date("Y") ?> Clear Web Concepts</p>
</footer>
<script src="assets/js/navigation.js"></script>
</body>
</html>

