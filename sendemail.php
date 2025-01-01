<?php
// send_email.php
require_once __DIR__ . '/../config/clearwebconfig.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once "vendor/autoload.php";

/*
 * Clear Web Concepts
 * Created by John R. Pepp
 * Date Created: July, 12, 2021
 * Last Revision: September 6, 2022 @ 8:00 AM
 * Version: 3.50 ßeta
 *
 */

use clearwebconcepts\{
    ErrorHandler,
    Database,
};


$errorHandler = new ErrorHandler();

// Register the exception handler method
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();
$mail = new PHPMailer(true); // Pass `true` to enable exceptions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputData = file_get_contents('php://input');
    $postData = json_decode($inputData, true);

    $name = $postData['name'];
    $email = $postData['email'];
    $comment = $postData['comments'];
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host = 'smtp.ionos.com ';                     //Set the SMTP server to send through
    $mail->SMTPAuth = true;                                   //Enable SMTP authentication
    $mail->Username = 'pepster@pepster.com';                     //SMTP username
    $mail->Password = EMAIL_PASSWORD;                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom( $email, $name);
    $mail->addAddress('PaulW@innsoltech.com', 'Paul Werner');     //Add a recipient
    $mail->addCC('jrpepp@pepster.com', 'John Pepp');
    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Inquiry of Bon Bon Wreaths Products';
    $mail->Body = $comment . " " .  $email;



    try {
        $mail->send();
        // Replace these variables with actual values based on your email sending process
        $status = 'success';
        $message = 'Email sent successfully';

        $response = array(
            'status' => $status,
            'message' => $message
        );
    } catch (Exception $e) {
        $response['status'] = 'error';
        $response['message'] = "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}