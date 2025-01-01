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

// Create an ErrorHandler instance
$errorHandler = new ErrorHandler();
// Set the exception handler to use the ErrorHandler instance
set_exception_handler([$errorHandler, 'handleException']);

// Create a Database instance and establish a connection
$database = new Database();
$pdo = $database->createPDO();
// Create a LoginRepository instance with the database connection
$login = new Login($pdo);
$checkStatus = new Login($pdo);

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to dashboard if the user is already logged in
if ($login->check_login_token()) {
    header('Location: dashboard.php');
    exit();
}

// Generate a CSRF token if it doesn't exist and store it in the session
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Detect environment
$isLocal = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
$cookieDomain = $isLocal ? '' : DOMAIN;
$cookieSecure = !$isLocal; // Set to true on remote server

// Process the login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the submitted CSRF token matches the one stored in the session
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        // Sanitize the username and password input
        $username = strip_tags($_POST['username']);
        $password = $_POST['password'];

        // Verify the user's credentials
        if ($login->verify_credentials($username, $password)) {
            // Generate a secure login token
            $token = bin2hex(random_bytes(32));
            // Store the login token in the database
            $login->store_token_in_database($_SESSION['user_id'], $token);

            // Set a secure cookie with the login token
            setcookie('login_token', $token, [
                'expires' => strtotime('+6 months'),
                'path' => '/',
                'domain' => $cookieDomain, // Adjusted for environment
                'secure' => $cookieSecure, // Adjusted for environment
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            // Store the login token in the session
            $_SESSION['login_token'] = $token;

            // Redirect the user to the dashboard
            header('Location: dashboard.php');
            exit;
        } else {
            // Log error message for invalid username or password
            $error = 'Invalid username or password';
            error_log("Login error: " . $error);
        }
    } else {
        // Display an error message
        $error = 'Invalid CSRF token';
        error_log("Login error: " . $error);
        $error = 'An error occurred. Please try again.';
    }
}

// Generate a random nonce value
$nonce = base64_encode(random_bytes(16));

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" media="all" href="assets/css/stylesheet.css">
</head>
<body class="site">
<?php include 'assets/includes/inc-header-nav.php'; ?>

<main class="main_container" itemprop="mainContentOfPage">
    <form class="login_style" method="post" action="login.php">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="screenName">
            <label class="text_username" for="username">Username</label>
            <input id="username" class="io_username" type="text" name="username" autocomplete="username" required>
        </div>
        <label class="text_password" for="password">Password</label>
        <input id="password" class="io_password" type="password" name="password" required>
        <div class="submitForm">
            <button class="submitBtn" id="submitForm" type="submit" name="submit" value="login">Login</button>
        </div>
    </form>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
</main>

<aside class="sidebar"></aside>
<footer class="colophon" itemprop="footer">
    <p>&copy; <?php echo date("Y") ?> Clear Web Concepts</p>
</footer>
<div id="cookie-banner" class="cookie-banner">
    <p>We use cookies to ensure you get the best experience on our website.
        <a href="/privacy-policy">Learn more</a></p>
    <button id="accept-cookies" class="cookie-button">Accept</button>
    <button id="reject-cookies" class="cookie-button">Reject</button>
</div>
<script src="assets/js/scripts.js"></script>
<script src="assets/js/navigation.js"></script>
</body>
</html>
