<?php

// Start output buffering to catch any unexpected output
ob_start();

// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/clearwebconfig.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "vendor/autoload.php";

use clearwebconcepts\{
    ErrorHandler,
    Database,
    Links,
    ImageContentManager,
    LoginRepository as Login
};

$errorHandler = new ErrorHandler();
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Debug: Log session status
error_log('Session status: ' . session_status());

// Get all headers and normalize keys to lowercase
$headers = array_change_key_case(getallheaders(), CASE_LOWER);
$csrfToken = isset($headers['csrf-token']) ? $headers['csrf-token'] : null;
error_log('Received Headers: ' . json_encode($headers));
error_log('CSRF Token from headers: ' . var_export($csrfToken, true));
error_log('CSRF Token from session: ' . var_export($_SESSION['csrf_token'], true));

if ($csrfToken === null) {
    error_log('CSRF Token not found in headers');
} else if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
    error_log('CSRF Token mismatch: Session token (' . $_SESSION['csrf_token'] . ') vs. Header token (' . $csrfToken . ')');
}

if ($csrfToken === null || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit();
}

// Debug: Log CSRF token check passed
error_log('CSRF token check passed');

$input = json_decode(file_get_contents('php://input'), true);
if (empty($input['email']) || empty($input['username']) || empty($input['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required']);
    exit();
}

// Debug: Log input validation passed
error_log('Input validation passed');

$email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
$username = htmlspecialchars($input['username']);
$password = $input['password'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email address']);
    exit();
}

// Debug: Log email validation passed
error_log('Email validation passed');

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Generate a verification token
$verificationToken = bin2hex(random_bytes(32));

// Debug: Log generated verification token
error_log('Generated verification token: ' . $verificationToken);

try {
    $stmt = $pdo->prepare("INSERT INTO admins (email, username, password, date_added, verification_token) VALUES (:email, :username, :password, NOW(), :verification_token)");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':verification_token', $verificationToken);
    $stmt->execute();

    // Send verification email
    $sendMailResult = sendVerificationEmail($email, $verificationToken);

    if ($sendMailResult['status'] === 'success') {
        // Debug: Log successful registration
        error_log('User registered successfully: ' . $username);
        http_response_code(201);
        echo json_encode(['message' => 'User registered successfully. Please check your email to verify your account.']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'User registered, but verification email could not be sent.']);
    }
} catch (PDOException $e) {
    // Debug: Log PDO exception
    error_log('PDOException: ' . $e->getMessage());

    if ($e->getCode() == 23000) { // Integrity constraint violation
        echo json_encode(['error' => 'Username or email already exists']);
    } else {
        echo json_encode(['error' => 'An error occurred. Please try again.']);
    }
}

// Flush output buffer and end buffering
ob_end_flush();

function sendVerificationEmail($email, $token) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.ionos.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'pepster@pepster.com';  // Your SMTP username
        $mail->Password = EMAIL_PASSWORD;         // Your SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Use a valid "From" address
        $mail->setFrom('pepster@pepster.com', 'Clear Web Concepts');  // Your valid "From" email address
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Email Verification';
        $mail->Body = "Please click the link below to verify your email address:<br><br>
            <a href='https://www.clearwebconcepts.com/verify.php?token=$token'>Verify Email</a>";

        $mail->send();

        // Debug: Log successful email sending
        error_log('Verification email sent to: ' . $email);

        return ['status' => 'success'];
    } catch (Exception $e) {
        // Debug: Log email sending error
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");

        return ['status' => 'error', 'message' => $mail->ErrorInfo];
    }
}


