<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";

// Import the ErrorHandler and Database classes from the PhotoTech namespace.
use clearwebconcepts\{
    ErrorHandler,
    Database,
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
if ($login->check_login_token()) {
    header('location: dashboard.php');
    exit();
}

?>
<!doctype html>
<html lang="en">
<head>
    <!-- Meta tags for responsiveness -->
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <!-- Title of the web page -->
    <title>Clear Web Concepts - Contact</title>
    <!-- Link to the external CSS file -->
    <link rel="stylesheet" media="all" href="assets/css/stylesheet.css">
</head>
<body class="site">
<?php include 'assets/includes/inc-header-nav.php'; ?>

<main class="main_container" itemprop="mainContentOfPage">
    <form class="contact" name="contact" action="contact.php" method="post" autocomplete="on">

        <!--                <input id="token" type="hidden" name="token" value="--><!--">-->
        <input type="hidden" name="reason" value="message">
        <figure class="owner">
            <img src="assets/images/img-portrait-john-002.jpg" alt="John Pepp - Clear Web Concepts LLC">
            <figcaption>John Pepp</figcaption>
        </figure>
        <hr class="horizontal_line">
        <div class="contact_name">
            <label class="labelstyle" for="name" accesskey="U">Contact Name</label>
            <input name="name" type="text" id="name" tabindex="1" placeholder="Full Name" autofocus
                   required="required">
        </div>

        <div class="contact_email">
            <label class="labelstyle" for="email" accesskey="E">Email</label>
            <input name="email" type="email" id="email" placeholder="Email" tabindex="2" required="required">
        </div>

        <div class="contact_phone">
            <label class="labelstyle" for="phone" accesskey="P">Phone <small>(optional)</small></label>
            <input name="phone" type="tel" id="phone" tabindex="3">
        </div>

        <div class="contact_website">
            <label class="labelstyle" for="web" accesskey="W">Product <small>(optional)</small></label>
            <input name="website" type="text" id="web" tabindex="4">
        </div>


        <div class="contact_comment">
            <label class="textareaLabel" for="comments">Comments Length:<span id="length"></span></label>
            <textarea name="comments" id="comments" spellcheck="true" placeholder="Inquiry on Clear Web Concepts" tabindex="6"
                      required="required"></textarea>
        </div>

        <div id="recaptcha" class="g-recaptcha" data-sitekey="6Le0QrobAAAAAGDacgiAr1UbkPmj0i-LFyWXocfg"
             data-callback="correctCaptcha"></div>

        <div id="message" class="notice">
            <img class="pen" src="assets/images/img-start-002.webp" alt="red light">
            <div id="successMessage" class="successStyle" style="display: none;color: #009578;">Email Successfully Sent!
            </div>
        </div>

        <button id="submitForm" class="submit_comment" type="submit" name="submit" value="Submit" tabindex="7"
                data-response="">Submit
        </button>
</main>

<aside class="sidebar">

</aside>

<footer class="colophon" itemprop="footer">
    <p>&copy;<?php echo date("Y") ?> Clear Web Concepts LLC <a href="polices.php">Polices</a> </p>
</footer>
<script src="assets/js/navigation.js"></script>
<script src="assets/js/contact.js"></script>
</body>
</html>
