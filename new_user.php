<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";

use clearwebconcepts\{
    ErrorHandler,
    Database,
    Links,
    ImageContentManager,
    LoginRepository as Login
};

// Create an ErrorHandler instance
$errorHandler = new ErrorHandler();
set_exception_handler([$errorHandler, 'handleException']);

// Create a Database instance and establish a connection
$database = new Database();
$pdo = $database->createPDO();

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Generate a CSRF token if it doesn't exist and store it in the session
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['csrf_token'];

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <title>Registration Page</title>
    <link rel="stylesheet" media="all" href="assets/css/stylesheet.css">
</head>
<body class="site">
<?php include 'assets/includes/inc-header-nav.php'; ?>

<main class="main_container" itemprop="mainContentOfPage">
    <form id="registrationForm" class="login_style">
        <div class="screenName">
            <label class="text_email" for="email">Email</label>
            <input id="email" class="io_email" type="email" name="email" required>
        </div>

        <div class="screenName">
            <label class="text_username" for="username">Username</label>
            <input id="username" class="io_username" type="text" name="username" required>
        </div>

        <label class="text_password" for="password">Password</label>
        <input id="password" class="io_password" type="password" name="password" required>

        <div class="submitForm">
            <button class="submitBtn" id="submitForm" type="submit">Register</button>
        </div>
    </form>
</main>

<aside class="sidebar"></aside>
<footer class="colophon" itemprop="footer">
    <p>&copy; <?php echo date("Y") ?> Clear Web Concepts</p>
</footer>
<script>
    document.getElementById('registrationForm').addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(event.target);
        const data = Object.fromEntries(formData.entries());

        const csrfToken = '<?php echo $csrfToken; ?>';
        console.log('CSRF Token:', csrfToken); // Debugging output

        try {
            const response = await fetch('register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'CSRF-Token': csrfToken // Ensure this header is included correctly
                },
                body: JSON.stringify(data)
            });

            const text = await response.text();
            console.log('Raw response:', text);

            const result = JSON.parse(text);

            if (response.ok) {
                alert('Registration successful!');
                window.location.href = 'login.php';
            } else {
                alert('Error: ' + result.error);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        }
    });
</script>
</body>
</html>
