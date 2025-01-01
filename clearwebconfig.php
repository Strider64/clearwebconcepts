<?php
// Set error reporting level
error_reporting(E_ALL);

// Disable display of errors
ini_set('display_errors', '0');

// Enable error logging
ini_set('log_errors', '1');

// Set the path for the error log file
ini_set('error_log', __DIR__ . '/error_log/error_log_file.log');


// Set session garbage collection time to the specified lifetime
ini_set('session.gc_maxlifetime', strtotime('+6 months'));

// Your other session settings...
session_set_cookie_params([
    'lifetime' => strtotime('+6 months'),
    'path' => '/',
    'domain' => 'www.clearwebconcepts.com',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();
if (empty($_SESSION['token'])) {
    try {
        $_SESSION['token'] = bin2hex(random_bytes(32));
    } catch (Exception $e) {
    }
}

const JWT_SECRET = '5ad3a6caeba6961749b06edeac36e2cf2a45156e9892b5df56c8da52fa133f2a';

ob_start(); // turn on output buffering
if($_SERVER["HTTPS"] != "on")
{
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}



ini_set('memory_limit', '512M'); // Increase to 512MB

$server_name = filter_input(INPUT_SERVER, 'SERVER_NAME', FILTER_SANITIZE_URL);
define('BASE_PATH', realpath(__DIR__));

const DOMAIN = 'www.clearwebconcepts.com';
const EMAIL_PASSWORD = 'Dpsimfm1983!1927';
const DATABASE_HOST = 'db5014736885.hosting-data.io';
const DATABASE_NAME = 'dbs12243984';
const DATABASE_USERNAME = 'dbu1569404'; //d329291176
const DATABASE_PASSWORD = 'Dpsimfm1983!';

// **PREVENTING SESSION HIJACKING**
header("Content-Type: text/html; charset=utf-8");
header('X-Frame-Options: SAMEORIGIN'); // Prevent Clickjacking:
header('X-Content-Type-Options: nosniff');
header('x-xss-protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

//header("content-security-policy: default-src 'self'; report-uri /csp_report_parser");
header('X-Permitted-Cross-Domain-Policies: master-only');
//header("Set-Cookie: mycookie=phototechguru; path=/phototech; domain=phototechguru.com; SameSite=Lax");
/* Get the current page */
$phpSelf = $_SERVER['PHP_SELF'];
$path_parts = pathinfo($phpSelf);
$basename = $path_parts['basename']; // Use this variable for action='':
$pageName = $path_parts['filename'];





