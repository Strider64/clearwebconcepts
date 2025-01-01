<?php
// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";

use clearwebconcepts\{
    ErrorHandler,
    Database
};

// Create an ErrorHandler instance
$errorHandler = new ErrorHandler();
set_exception_handler([$errorHandler, 'handleException']);

// Create a Database instance and establish a connection
$database = new Database();
$pdo = $database->createPDO();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get the verification token from the query string
$token = $_GET['token'] ?? '';

if (empty($token)) {
    die('Invalid verification token');
}

try {
    // Check if the token exists in the database
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE verification_token = :token");
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Token is valid, update the user's status
        $stmt = $pdo->prepare("UPDATE admins SET verification_token = NULL, email_verified = 1, security = 'member' WHERE id = :id");
        $stmt->bindParam(':id', $user['id']);
        $stmt->execute();

        // Set the session and cookies if needed
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Generate a secure login token
        $loginToken = bin2hex(random_bytes(32));

        // Store the login token in the database
        $stmt = $pdo->prepare("UPDATE admins SET token = :token WHERE id = :id");
        $stmt->bindParam(':token', $loginToken);
        $stmt->bindParam(':id', $user['id']);
        $stmt->execute();

        // Set a secure cookie with the login token
        setcookie('login_token', $loginToken, [
            'expires' => strtotime('+6 months'),
            'path' => '/',
            'domain' => 'clearwebconcepts.com',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        // Store the login token in the session
        $_SESSION['login_token'] = $loginToken;

        // Redirect to the appropriate page based on security level
        if ($user['security'] === 'sysop') {
            header('Location: dashboard.php'); // Redirect to admin dashboard
        } else {
            header('Location: member.php'); // Redirect to member page
        }
        exit();
    } else {
        die('Invalid or expired verification token');
    }
} catch (PDOException $e) {
    error_log('PDOException: ' . $e->getMessage());
    die('An error occurred. Please try again.');
}

